<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit;

use Attestto\SolanaPhpSdk\Account;
use Attestto\SolanaPhpSdk\Keypair;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Attestto\SolanaPhpSdk\Programs\DidSolProgram;
use Attestto\SolanaPhpSdk\SolanaRpcClient;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\Transaction;

class SnsBurnTest extends TestCase
{

 
  
  
     /** @test */
    public function it_burns_domain()
    {
        // $tx = new Transaction();
        // $ix = burnDomain("1automotive", OWNER, OWNER);
        // $tx.add(ix);
        // const { blockhash } = await connection.getLatestBlockhash();
        // tx.recentBlockhash = blockhash;
        // tx.feePayer = OWNER;
        // const res = await connection.simulateTransaction(tx);
        // $owner = new PublicKey("Fxuoy3gFjfJALhwkRcuKjRdechcgffUApeYAfMWck6w8");
        // $base64Data = self::DID_DATA;
        // $didData = DidSolProgram::deserializeDidData($base64Data);

        // $this->assertEquals($didData->keyData, self::DID_SUBJECT_PK);
        $this->markTestSkipped('TODO once StakeProgram is implemented');
    }


}
