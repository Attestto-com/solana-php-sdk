<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit;

use Attestto\SolanaPhpSdk\Programs\SNS\Utils;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\Exceptions\SNSError;
use Attestto\SolanaPhpSdk\Tests\TestCase;

class UtilsTest extends TestCase
{
    public function testGetHashedNameSync()
    {
        $utils = new Utils();
        $name = 'example';
        $expectedHash = hash('sha256', $utils->config['HASH_PREFIX'] . $name);

        $hashedName = $utils->getHashedNameSync($name);

        $this->assertEquals($expectedHash, $hashedName);
    }

    public function testGetNameAccountKeySync()
    {
        $utils = new Utils();
        $name = 'example';
        $hashedName = $utils->getHashedNameSync($name);
        $nameClass = new PublicKey('name_class');
        $nameParent = new PublicKey('name_parent');
        $expectedNameAccountKey = new PublicKey('name_account_key');


        $nameAccountKey = $utils->getNameAccountKeySync($hashedName, $nameClass, $nameParent);
        dd($nameAccountKey);
        $this->assertEquals($expectedNameAccountKey, $nameAccountKey);
    }

    public function testReverseLookup()
    {
        $utils = new Utils();
        $connection = $this->createMock(Connection::class);
        $nameAccount = new PublicKey('name_account');
        $hashedReverseLookup = hash('sha256', $nameAccount->toBase58());
        $reverseLookupAccount = new PublicKey('reverse_lookup_account');
        $expectedDomain = 'example.com';

        // Mock the getHashedNameSync method
        $utils->method('getHashedNameSync')
            ->willReturn($hashedReverseLookup);

        // Mock the getNameAccountKeySync method
        $utils->method('getNameAccountKeySync')
            ->willReturn($reverseLookupAccount);

        // Mock the retrieve method of NameRegistryStateAccount
        $mockRegistry = $this->createMock(NameRegistryStateAccount::class);
        $mockRegistry->method('retrieve')
            ->willReturn(['data' => 'example.com']);

        $this->expectException(SNSError::class);
        $this->expectExceptionCode(SNSError::NoAccountData);

        $domain = $utils->reverseLookup($connection, $nameAccount);

        $this->assertEquals($expectedDomain, $domain);
    }

    public function testDeserializeReverse()
    {
        $utils = new Utils();
        $data = "\x00\x00\x00\x07example";
        $expectedName = 'example';

        $name = $utils->deserializeReverse($data);

        $this->assertEquals($expectedName, $name);
    }

    public function testGetDomainKeySyncNoRecord()
    {
        $utils = new Utils();
        $domain = 'example.sol';
        $record = 'V2';
        $expectedResult = [
            'pubkey' => 'domain_key',
            'isSub' => true,
            'parent' => 'parent_key',
            'isSubRecord' => true
        ];

        $result = $utils->getDomainKeySync($domain, $record);

        $this->assertEquals($expectedResult, $result);
    }
}