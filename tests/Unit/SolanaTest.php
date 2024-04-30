<?php

namespace Attestto\SolanaPhpSdk\SNS;

use Attestto\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Attestto\SolanaPhpSdk\Exceptions\GenericException;
use Attestto\SolanaPhpSdk\Programs\SystemProgram;
use Attestto\SolanaPhpSdk\SolanaRpcClient;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\MockObject\Exception;
use Random\RandomException;

// Add missing import statement

class SolanaTest extends TestCase
{
    /** @test
     * @throws AccountNotFoundException
     */
    public function it_will_throw_exception_when_rpc_account_response_is_null()
    {

        $client = $this->assembleClient('POST', []);

        $solana = new SystemProgram($client);

        $this->expectException(AccountNotFoundException::class);
        $solana->getAccountInfo('abc123');
    }
}