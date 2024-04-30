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


        $mockHandler = new MockHandler([
            new Response(
                200,
                [],
                json_encode([
                    'jsonrpc' => '2.0',
                    'result' => [
                        'context' =>  [
                            'slot' => 6440,
                        ],
                        'value' => null, // no account data.
                    ],
                    'id' => 1, // Set response ID different from the random key
                ])
            ),
        ]);
        $client = $this->assembleClient($mockHandler);
        $solana = new SystemProgram($client);

        $this->expectException(GenericException::class);
        $solana->getAccountInfo('abc123');
    }
}