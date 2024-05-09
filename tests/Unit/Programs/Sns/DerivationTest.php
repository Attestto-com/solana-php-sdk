<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit\Programs\SNS;


use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\Exceptions\InputValidationException;

use Attestto\SolanaPhpSdk\Exceptions\SNSError;
use Attestto\SolanaPhpSdk\Programs\SnsProgram;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use Attestto\SolanaPhpSdk\SolanaRpcClient;
use PHPUnit\Framework\MockObject\Exception;

class DerivationTest extends TestCase
{

    private array $items = [
        [
            'domain' => 'bonfida',
            'address' => 'Crf8hzfthWGbGbLTVCiqRqV5MVnbpHB1L9KQMd6gsinb',
        ],
        [
            'domain' => 'bonfida.sol',
            'address' => 'Crf8hzfthWGbGbLTVCiqRqV5MVnbpHB1L9KQMd6gsinb',
        ],
        [
            'domain' => 'dex.bonfida',
            'address' => 'HoFfFXqFHAC8RP3duuQNzag1ieUwJRBv1HtRNiWFq4Qu',
        ],
        [
            'domain' => 'dex.bonfida.sol',
            'address' => 'HoFfFXqFHAC8RP3duuQNzag1ieUwJRBv1HtRNiWFq4Qu',
        ],
    ];

    public function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function test_getHashedNameSync(){
        $client = $this->createMock(SolanaRpcClient::class);
        $sns = new SnsProgram($client);
        $hashedName = $sns->getHashedNameSync('bonfida');
        $bs58HashedName = $hashedName->toBase58String();
        $this->assertEquals('AcmVjPtaDyNboWGSKjYHxea1QDgN648T4Je3HUpkHecf', $bs58HashedName);
    }

    /**
     * @throws InputValidationException
     */
    #[Test]
    public function test_deriveSynch(){
        $client = $this->createMock(SolanaRpcClient::class);
        $sns = new SnsProgram($client);
        $hashedName = $sns->_deriveSync('bonfida');
        $nameAccountKey = $sns->getNameAccountKeySync($hashedName['hashed']);
        $nameAccountKeyBs58 = $nameAccountKey->toBase58();
        $this->assertEquals('85v6oF1VnGNeT4oV2fH8HpVBj3k3U4m6uNnWYT8AcA5H',$nameAccountKeyBs58 );
    }

    #[Test]
    public function test_getDomainKeySync()
    {
        $client = $this->createMock(SolanaRpcClient::class);
        $sns = new SnsProgram($client);
        foreach ($this->items as $item) {
            $result = $sns->getDomainKeySync($item['domain']);
            $this->assertInstanceOf(PublicKey::class, $result['pubkey']);
            $this->assertEquals($item['address'], $result['pubkey']->toBase58());
        }
    }

    /**
     * @throws SNSError
     * @throws Exception
     * @throws InputValidationException
     */
    #[Test]
    public function test_getReverseKeySync()
    {
        $client = $this->createMock(SolanaRpcClient::class);
        $sns = new SnsProgram($client);
        foreach ($this->items as $item) {
            $result = $sns->getReverseKeySync($item['domain']);
            $this->assertInstanceOf(PublicKey::class, $result);
            $this->assertTrue(
                $result->toBase58() === 'DqgmWxe2PPrfy45Ja3UPyFGwcbRzkRuwXt3NyxjX8krg' ||
                $result->toBase58() === 'BrRErziYEA9oBoDyYrdVF9p6Gs1QtdpaZ6AQpaybeZgf'
            );
        }
    }

    #[Test]
    public function test_getNameOwner()
    {
        $client = new SolanaRpcClient('https://api.mainnet-beta.solana.com');
        $connection = new Connection($client);
        $sns = new SnsProgram($client);
        $nameAccountKey = 'HoFfFXqFHAC8RP3duuQNzag1ieUwJRBv1HtRNiWFq4Qu';
        $result = $sns->getNameOwner($connection, $nameAccountKey);
        $owner = $result['registry']->owner;
        $parent = $result['registry']->parentName;
        $this->assertInstanceOf(PublicKey::class, $owner);
        $this->assertEquals('CnNHzcp7L4jKiA2Rsca3hZyVwSmoqXaT8wGwzS8WvvB2', $owner->toBase58());
        $this->assertEquals('Crf8hzfthWGbGbLTVCiqRqV5MVnbpHB1L9KQMd6gsinb', $parent->toBase58());
    }

}
