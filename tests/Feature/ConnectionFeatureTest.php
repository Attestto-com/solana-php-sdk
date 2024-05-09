<?php

namespace Attestto\SolanaPhpSdk\Tests\Feature;

use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Attestto\SolanaPhpSdk\Exceptions\GenericException;
use Attestto\SolanaPhpSdk\Exceptions\InputValidationException;
use Attestto\SolanaPhpSdk\Exceptions\InvalidIdResponseException;
use Attestto\SolanaPhpSdk\Exceptions\MethodNotFoundException;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use Attestto\SolanaPhpSdk\SolanaRpcClient;
use Attestto\SolanaPhpSdk\Transaction;
use Attestto\SolanaPhpSdk\Keypair;
use Attestto\SolanaPhpSdk\Programs\SystemProgram;
use Attestto\SolanaPhpSdk\Util\Buffer;
use Attestto\SolanaPhpSdk\Util\Commitment;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Random\RandomException;
use SodiumException;

class ConnectionFeatureTest extends TestCase
{


    /**
     * @throws AccountNotFoundException|Exception
     * @throws RandomException
     */
    public function testGetAccountInfoFeature()
    {
        $client = new SolanaRpcClient($this->endpoint);
        $connection = new Connection($client);
        // Act: Call the getAccountInfo method with a real public key
        $pubKey = 'Atts2CLVXirnDsai6tCttdnAAyFwLqxqUd8zYbobgWCf';
        $result = $connection->getAccountInfo($pubKey);

        // Assert: Check the result is as expected
        // This will depend on what the actual response from the Solana API looks like
        $this->assertEquals('11111111111111111111111111111111', $result['owner']);
    }

    public function testGetTransaction()
    {
        $client = new SolanaRpcClient($this->endpoint);
        $connection = new Connection($client);
        // Act: Call the getAccountInfo method with a real public key
        $txn = '3ScP26YbYarMTQBA6i3a9NynrXj845FNX3afmgTWiZAAhqVrZwyw5YbMhuqczamjBLwWZ3XNY91nrRCeVNMjtexE';
        $result = $connection->getTransaction($txn);


        $this->assertEquals('3ScP26YbYarMTQBA6i3a9NynrXj845FNX3afmgTWiZAAhqVrZwyw5YbMhuqczamjBLwWZ3XNY91nrRCeVNMjtexE', $result['transaction']['signatures'][0]);
    }

    /**
     * @throws Exception
     */
    public function testGetLatestBlockhash()
    {
        $client = new SolanaRpcClient($this->endpoint);
        $connection = new Connection($client);

        $result = $connection->getLatestBlockhash(Commitment::finalized());

        $this->assertNotNull($result['blockhash']);


    }

    public function testGeRecentBlockhash()
    {
        $client = new SolanaRpcClient($this->endpoint);
        $connection = new Connection($client);
        $commitment = new Commitment('finalized');
        $result = $connection->getRecentBlockhash($commitment);

        $this->assertNotNull($result['blockhash']);


    }

    public function testGetConfirmedTransaction()
    {
        $client = new SolanaRpcClient($this->endpoint);
        $connection = new Connection($client);

        $txn = '3ScP26YbYarMTQBA6i3a9NynrXj845FNX3afmgTWiZAAhqVrZwyw5YbMhuqczamjBLwWZ3XNY91nrRCeVNMjtexE';
        $result = $connection->getConfirmedTransaction($txn);
        $this->assertEquals('3ScP26YbYarMTQBA6i3a9NynrXj845FNX3afmgTWiZAAhqVrZwyw5YbMhuqczamjBLwWZ3XNY91nrRCeVNMjtexE', $result['transaction']['signatures'][0]);
    }



        /**
     * @throws GenericException
     * @throws SodiumException
         * @throws InputValidationException
         */
    public function testSimulateTransaction()
    {

        $client = new SolanaRpcClient($this->endpoint);
        $connection = new Connection($client);

        $account1 = Keypair::generate();
        $account2 = Keypair::generate();
        $recentBlockhash = $connection->getLatestBlockhash(Commitment::finalized())['blockhash'];

        $transfer1 = SystemProgram::transfer($account1->getPublicKey(), $account2->getPublicKey(), 123);
        $transfer2 = SystemProgram::transfer($account2->getPublicKey(), $account1->getPublicKey(), 123);

        $orgTransaction = new Transaction($recentBlockhash);
        $orgTransaction->add($transfer1, $transfer2);
        $orgTransaction->sign($account1, $account2);

        $newTransaction = new Transaction($orgTransaction->recentBlockhash, null, null, $orgTransaction->signatures);
        $newTransaction->add($transfer1, $transfer2);


        $response = $connection->simulateTransaction($newTransaction, [$account1, $account2]);


        $this->assertEquals('AccountNotFound', $response['value']['err']);

    }

    /**
     * @throws InvalidIdResponseException
     * @throws RandomException
     * @throws GenericException
     * @throws SodiumException
     * @throws MethodNotFoundException
     * @throws ClientExceptionInterface
     */
    #[Test]
    public function testSendTransaction()
    {

        $client = new SolanaRpcClient($this->endpoint);
        $connection = new Connection($client);
        // ABCexcAcjLuEsZUbaudqATgUp4MUL5STNAjr3goRLk6Y -- must have sol ( airdrop sol )
        $secretKey = json_decode('[45,54,39,107,89,97,142,99,78,79,179,20,100,88,176,123,63,144,15,102,152,62,187,243,16,83,234,7,115,196,73,58,136,86,43,13,28,152,130,148,70,247,159,0,0,197,176,80,47,230,51,124,29,148,39,41,36,61,88,254,63,143,109,69]');

        $account1 = Keypair::fromSecretKey($secretKey);
        $account2 = new PublicKey('BURNKKWBSaXmUFQPaABzWWtQ97U2oByNtPiXz3cCAMpq');

        $transfer1 = SystemProgram::transfer($account1->getPublicKey(), $account2->getPublicKey(), 123);

        $orgTransaction = new Transaction();

        $orgTransaction->add($transfer1);

        $response = $connection->sendTransaction($orgTransaction, [$account1], ['skipPreflight' => false]);
        $this->assertIsString($response);

        $tx2 = new Transaction();
        $tx2->add($transfer1);
        $response = $connection->sendTransaction($tx2, [$account1]);
        $this->assertIsString($response);


    }



    /**
     * @throws AccountNotFoundException
     * @throws ClientExceptionInterface
     * @throws InputValidationException
     * @throws GenericException
     * @throws InvalidIdResponseException
     * @throws MethodNotFoundException
     */

    #[Test]
    public function testGetBalance()
    {
        $client = new SolanaRpcClient($this->endpoint);
        $connection = new Connection($client);
        $receiverPublicKey = 'ABCexcAcjLuEsZUbaudqATgUp4MUL5STNAjr3goRLk6Y';

        $result = $connection->getBalance($receiverPublicKey);
        $this->assertIsFloat($result);
    }

    #[Test]
    public function test_getMinimumBalanceForRentExemption(){
        $client = new SolanaRpcClient($this->endpoint);
        $connection = new Connection($client);
        $result = $connection->getMinimumBalanceForRentExemption(2000);
        $this->assertIsInt($result);
    }



}
