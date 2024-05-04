<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit;

use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Attestto\SolanaPhpSdk\Exceptions\GenericException;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use Attestto\SolanaPhpSdk\SolanaRpcClient;
use Attestto\SolanaPhpSdk\Transaction;
use Attestto\SolanaPhpSdk\KeyPair;
use Attestto\SolanaPhpSdk\Programs\SystemProgram;
use PHPUnit\Framework\MockObject\Exception;
use SodiumException;


class ConnectionFeatureTest extends TestCase
{
    /**
     * @throws AccountNotFoundException|Exception
     */
    public function testGetAccountInfo()
    {
        $pubKey = '3Wnd5Df69KitZfUoPYZU438eFRNwGHkhLnSAWL65PxJX';
        $accountInfo = ['anything'];

        $clientMock = $this->createMock(SolanaRpcClient::class);
        $clientMock->expects($this->once())
            ->method('call')
            ->with('getAccountInfo', [$pubKey, ["encoding" => "jsonParsed"]])
            ->willReturn(['value' => $accountInfo]);

        $connection = new Connection($clientMock);

        $result = $connection->getAccountInfo($pubKey);
        $this->assertEquals($accountInfo, $result);
    }

    /**
     * @throws Exception
     */
    public function testGetAccountInfoThrowsException()
    {
        $pubKey = 'your_public_key_here';

        $clientMock = $this->createMock(SolanaRpcClient::class);
        $clientMock->expects($this->once())
            ->method('call')
            ->with('getAccountInfo', [$pubKey, ["encoding" => "jsonParsed"]])
            ->willReturn(['value' => null]);

        $connection = new Connection($clientMock);

        $this->expectException(AccountNotFoundException::class);
        $this->expectExceptionMessage("API Error: Account $pubKey not found.");
        $connection->getAccountInfo($pubKey);

    }

    /**
     * @throws GenericException
     * @throws SodiumException
     */
    public function testSimulateTransaction()
    {
        $account1 = Keypair::generate();
        $account2 = Keypair::generate();
        $recentBlockhash = $account1->getPublicKey()->toBase58(); // Fake recentBlockhash

        $transfer1 = SystemProgram::transfer($account1->getPublicKey(), $account2->getPublicKey(), 123);
        $transfer2 = SystemProgram::transfer($account2->getPublicKey(), $account1->getPublicKey(), 123);

        $orgTransaction = new Transaction($recentBlockhash);
        $orgTransaction->add($transfer1, $transfer2);
        $orgTransaction->sign($account1, $account2);

        $newTransaction = new Transaction($orgTransaction->recentBlockhash, null, null, $orgTransaction->signatures);
        $newTransaction->add($transfer1, $transfer2);

        // TODO - Fix this test, call the method and compare the transactions

        $this->assertEquals($orgTransaction, $newTransaction);

    }

    #[Test]
    public function testGetBalance()
    {
        $pubKey = '3Wnd5Df69KitZfUoPYZU438eFRNwGHkhLnSAWL65PxJX';
        $balance = 100;

        $clientMock = $this->createMock(SolanaRpcClient::class);
        $clientMock->expects($this->once())
            ->method('call')
            ->with('getBalance', [$pubKey])
            ->willReturn(['value' => $balance]);

        $connection = new Connection($clientMock);

        $result = $connection->getBalance($pubKey);
        $this->assertEquals($balance, $result);
    }



    #[Test]
    public function testGetRecentBlockhash()
    {
        $commitment = 'confirmed';
        $blockhash = ['anything'];
    }

    #[Test]
    public function testGetLatestBlockhash()
    {
        $commitment = 'confirmed';
        $blockhash = ['anything'];
    }

    #[Test]
    public function testRequestAirDrop()
    {
        $pubKey = '3Wnd5Df69KitZfUoPYZU438eFRNwGHkhLnSAWL65PxJX';
        $lamports = 100;
        $transaction = ['anything'];

        $clientMock = $this->createMock(SolanaRpcClient::class);
        $clientMock->expects($this->once())
            ->method('call')
            ->with('requestAirdrop', [$pubKey, $lamports])
            ->willReturn(['value' => $transaction]);

        $connection = new Connection($clientMock);

        $result = $connection->requestAirdrop($pubKey, $lamports);
        $this->assertEquals($transaction, $result);
    }



}
