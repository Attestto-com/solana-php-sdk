<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit\Programs;

use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Attestto\SolanaPhpSdk\Exceptions\GenericException;
use Attestto\SolanaPhpSdk\Exceptions\InputValidationException;
use Attestto\SolanaPhpSdk\Keypair;
use Attestto\SolanaPhpSdk\Programs\SplToken\State\Account;
use Attestto\SolanaPhpSdk\Programs\SplTokenProgram;
use Attestto\SolanaPhpSdk\Programs\SystemProgram;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\SolanaRpcClient;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;


class SplProgramTest extends TestCase
{

    private SystemProgram $program;

    #[Test]
    public function testGetTokenAccountsByOwner()
    {
        $splProgram =  new SplTokenProgram(new SolanaRpcClient('https://api.devnet.solana.com'));
        $result = $splProgram->getTokenAccountsByOwner('Atts2CLVXirnDsai6tCttdnAAyFwLqxqUd8zYbobgWCf');
        $this->assertNotNull( $result['value'][0]['pubkey']);
    }

    #[Test]
    public function testGetAssociatedTokenAddressSync()
    {
        $splProgram =  new SplTokenProgram(new SolanaRpcClient('https://api.devnet.solana.com'));
        $mint = new PublicKey('So11111111111111111111111111111111111111112');
        $owner = new PublicKey('ABCexcAcjLuEsZUbaudqATgUp4MUL5STNAjr3goRLk6Y');
        $result = $splProgram->getAssociatedTokenAddressSync($mint, $owner, false);
        $this->assertEquals('8mFzQabNJVPstQHUFn7wqgvZyrxey3Qn7g2axD6roJCT', $result->toBase58());
        $splProgram =  new SplTokenProgram(new SolanaRpcClient('https://api.devnet.solana.com'));
        $owner2 = new PublicKey('Atts2CLVXirnDsai6tCttdnAAyFwLqxqUd8zYbobgWCf');
        $result2 = $splProgram->getAssociatedTokenAddressSync($mint, $owner2, true );
        $this->assertEquals('AmDBTASE8BPvtAqgAPKeihZPgLqGMSWcStMbYbvZBmhk', $result2->toBase58());
    }

    #[Test]
    public function testGetAccount()
    {
        $client = new SolanaRpcClient('https://api.devnet.solana.com');
        $connection = new Connection($client);
        $account = Account::getAccount($connection, new PublicKey('AmDBTASE8BPvtAqgAPKeihZPgLqGMSWcStMbYbvZBmhk'));
        $this->assertNotNull($account);
     }

    #[Test]
    public function testGetOrCreateAssociatedTokenAccount()
    {
        $client = new SolanaRpcClient('https://api.devnet.solana.com');
        $connection = new Connection($client);
        $splProgram =  new SplTokenProgram($client);
        // ABCexcAcjLuEsZUbaudqATgUp4MUL5STNAjr3goRLk6Y -- must have sol ( airdrop sol )
        $secretKey = json_decode('[45,54,39,107,89,97,142,99,78,79,179,20,100,88,176,123,63,144,15,102,152,62,187,243,16,83,234,7,115,196,73,58,136,86,43,13,28,152,130,148,70,247,159,0,0,197,176,80,47,230,51,124,29,148,39,41,36,61,88,254,63,143,109,69]');
        $payerSigner = Keypair::fromSecretKey($secretKey);
        $signer = $payerSigner->getPublicKey()->toBase58();

        $this->assertEquals('ABCexcAcjLuEsZUbaudqATgUp4MUL5STNAjr3goRLk6Y', $signer);
        // A new account with SOL (airdrop Sol)
        $owner = new PublicKey('ABCRVMBm2LBCVTxVuuxzwYiMqX8NTp6zzH9Tr6V2ZaJg');
        // ATA DiRmKFukTVSAAGPmCFeH4ZEV6BtUcshZuACUF6Wp2ifL
        $mint = new PublicKey('So11111111111111111111111111111111111111112');
        $account = $splProgram->getOrCreateAssociatedTokenAccount(
            $connection,
            $payerSigner,
            $mint,
            $owner,
            true);
        $accountAddress = $account->address->toBase58();
        $this->assertEquals('DiRmKFukTVSAAGPmCFeH4ZEV6BtUcshZuACUF6Wp2ifL', $accountAddress);
    }
    #[Test]
    public function testGetOrCreateAssociatedTokenAccountDoesNotExist()
    {
        $client = new SolanaRpcClient('https://api.devnet.solana.com');
        $connection = new Connection($client);
        $splProgram =  new SplTokenProgram($client);
        // ABCexcAcjLuEsZUbaudqATgUp4MUL5STNAjr3goRLk6Y -- must have sol ( airdrop sol )
        $secretKey = json_decode('[45,54,39,107,89,97,142,99,78,79,179,20,100,88,176,123,63,144,15,102,152,62,187,243,16,83,234,7,115,196,73,58,136,86,43,13,28,152,130,148,70,247,159,0,0,197,176,80,47,230,51,124,29,148,39,41,36,61,88,254,63,143,109,69]');
        $payerSigner = Keypair::fromSecretKey($secretKey);
        $signer = $payerSigner->getPublicKey()->toBase58();

        $this->assertEquals('ABCexcAcjLuEsZUbaudqATgUp4MUL5STNAjr3goRLk6Y', $signer);
        // A new account with SOL (airdrop Sol)

        $owner = new Keypair();
        // ATA Random
        $mint = new PublicKey('So11111111111111111111111111111111111111112');
        try {
            $splProgram->getOrCreateAssociatedTokenAccount(
                $connection,
                $payerSigner,
                $mint,
                $owner->getPublicKey(),
                true);
        } catch (AccountNotFoundException $e) {
            $this->assertTrue(true);
            return;
        } catch (GenericException $e) {
            $this->assertTrue(true);
            return;
        }
        $this->fail('Expected AccountNotFoundException or GenericException not thrown');

    }

    /**
     * @throws InputValidationException
     */
    #[Test]
    public function testCreateSyncNativeInstruction()
    {
        $splProgram =  new SplTokenProgram(new SolanaRpcClient('https://api.devnet.solana.com'));
        $owner = new PublicKey('ABCexcAcjLuEsZUbaudqATgUp4MUL5STNAjr3goRLk6Y');
        $syncNativeIx = $splProgram->createSyncNativeInstruction($owner);
        $this->assertNotNull($syncNativeIx);
        $this->assertEquals(17, count($syncNativeIx->data));
    }

    /**
     * @throws InputValidationException
     */
    #[Test]
    public function testCreateAssociatedTokenAccountInstruction()
    {
        $splProgram =  new SplTokenProgram(new SolanaRpcClient('https://api.devnet.solana.com'));
        $payer = new PublicKey('ABCexcAcjLuEsZUbaudqATgUp4MUL5STNAjr3goRLk6Y');
        $associatedToken = new PublicKey('DiRmKFukTVSAAGPmCFeH4ZEV6BtUcshZuACUF6Wp2ifL');
        $owner = new PublicKey('ABCRVMBm2LBCVTxVuuxzwYiMqX8NTp6zzH9Tr6V2ZaJg');
        $mint = new PublicKey('So11111111111111111111111111111111111111112');
        $programId = new PublicKey(SplTokenProgram::TOKEN_PROGRAM_ID);
        $associatedTokenProgramId   = new PublicKey(SplTokenProgram::ASSOCIATED_TOKEN_PROGRAM_ID);

        $ix = $splProgram->createAssociatedTokenAccountInstruction(
            $payer,
            $associatedToken,
            $owner,
            $mint,
            $programId,
            $associatedTokenProgramId
        );
        $this->assertNotNull($ix);
    }


}
