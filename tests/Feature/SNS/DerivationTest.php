<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit\Programs\SNS;

use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\Util\Buffer;
use Attestto\SolanaPhpSdk\Borsh\Borsh;
use Attestto\SolanaPhpSdk\Programs\SNS\InstructionBurn;
use Attestto\SolanaPhpSdk\Programs\SNS\Utils;

class DerivationTest extends TestCase
{

    private $items = [
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

    /** @test */
    public function it_derives_domain_key()
    {
        $utils = new Utils();
        foreach ($this->items as $item) {
            $result = $utils->getDomainKey($item['domain']);
            $this->assertInstanceOf(PublicKey::class, $result['pubkey']);
            $this->assertEquals($item['address'], $result['pubkey']->toBase58());
        }

        foreach ($this->items as $item) {
            $result = $utils->getDomainKeySync($item['domain']);
            $this->assertInstanceOf(PublicKey::class, $result['pubkey']);
            $this->assertEquals($item['address'], $result['pubkey']->toBase58());
        }
    }
  
}