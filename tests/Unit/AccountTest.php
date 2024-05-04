<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit;

use Attestto\SolanaPhpSdk\Account;
use Attestto\SolanaPhpSdk\Keypair;
use Attestto\SolanaPhpSdk\Programs\SplTokenProgram;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\SolanaRpcClient;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AccountTest extends TestCase
{
    #[Test]
    public function test_it_generate_new_account()
    {
        $account = new Account();

        $this->assertEquals(64, count($account->getSecretKey()));
    }

    #[Test]
    public function test_it_account_from_secret_key()
    {
        $secretKey = [
            153, 218, 149, 89, 225, 94, 145, 62, 233, 171, 46, 83, 227, 223, 173, 87,
            93, 163, 59, 73, 190, 17, 37, 187, 146, 46, 51, 73, 79, 73, 136, 40, 27,
            47, 73, 9, 110, 62, 93, 189, 15, 207, 169, 192, 192, 205, 146, 217, 171,
            59, 33, 84, 75, 52, 213, 221, 74, 101, 217, 139, 135, 139, 153, 34,
        ];

        $account = new Account($secretKey);

        $this->assertEquals('2q7pyhPwAwZ3QMfZrnAbDhnh9mDUqycszcpf86VgQxhF', $account->getPublicKey()->toBase58());
    }

    #[Test]
    public function test_it_account_keypair()
    {
        $expectedAccount = new Account();
        $keypair = Keypair::fromSecretKey($expectedAccount->getSecretKey());

        $derivedAccount = new Account($keypair->getSecretKey());
        $this->assertEquals($expectedAccount->getPublicKey(), $derivedAccount->getPublicKey());
        $this->assertEquals($expectedAccount->getSecretKey(), $derivedAccount->getSecretKey());
    }

}
