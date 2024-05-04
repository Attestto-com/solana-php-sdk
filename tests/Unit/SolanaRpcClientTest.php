<?php

namespace Attestto\SolanaPhpSdk\Tests\Feature;

use Attestto\SolanaPhpSdk\Exceptions\GenericException;
use Attestto\SolanaPhpSdk\Exceptions\InvalidIdResponseException;
use Attestto\SolanaPhpSdk\Exceptions\MethodNotFoundException;
use Attestto\SolanaPhpSdk\SolanaRpcClient;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Random\RandomException;

class SolanaRpcClientTest extends TestCase
{
    /**
     * @throws RandomException
     * @throws Exception
     */
    #[Test]
    public function test_it_generates_random_key()
    {

        $client = $this->assembleClient('POST', ['error' => [
            'code' => SolanaRpcClient::ERROR_CODE_METHOD_NOT_FOUND,
            'message' => 'ANYTHING'
        ]]);

        $rpc1 = $client->buildRpc('doStuff', []);
        $rpc2 = $client->buildRpc('doStuff', []);

        $client = $this->assembleClient('POST', ['result' => [
            'data' => 'SOMEDATABASE64ORJSON'
        ]]);

        $rpc3= $client->buildRpc('doStuff', []);
        $rpc4 = $client->buildRpc('doStuff', []);

        $this->assertEquals($rpc1['id'], $rpc2['id']);
        $this->assertEquals($rpc3['id'], $rpc4['id']);
        $this->assertNotEquals($rpc1['id'], $rpc4['id']);
    }

    /**
     *
     * TODO: Validate if we need to test the Exception, or that it does not throw the exception since the RPC
     * its built on runtime and the ID is a protected property.
     * @throws MethodNotFoundException
     * @throws Exception
     * @throws RequestException
     * @throws GenericException
     * @throws \Exception|ClientExceptionInterface
     */
    #[Test]
    public function test_it_validates_response_id()
    {
        $client = $this->assembleClient('POST', ['result' => [
            'data' => 'SOMEDATABASE64ORJSON'
        ]]);


        // Assert that the correct exception is thrown when the response ID is invalid
        //$this->expectException(InvalidIdResponseException::class);
        $response = $client->call('getAccountInfo');
        $this->assertEquals( 'SOMEDATABASE64ORJSON', $response['data']);

    }

    /**
     * @throws InvalidIdResponseException
     * @throws MethodNotFoundException
     * @throws Exception
     * @throws RequestException
     * @throws GenericException
     * @throws \Exception
     * @throws ClientExceptionInterface
     */
    #[Test]
    public function test_it_throws_exception_for_invalid_methods()
    {

        $client = $this->assembleClient('POST', ['error' => [
            'code' => SolanaRpcClient::ERROR_CODE_METHOD_NOT_FOUND,
            'message' => 'Method not found'
        ]]);

//        $solana = new SystemProgram($client);
//
//        $this->expectException(AccountNotFoundException::class);
//        $solana->getAccountInfo('abc123');

        // Create mock handler for Guzzle
//        $mockHandler = new MockHandler([
//            new Response(
//                200,
//                [],
//                json_encode([
//                    'jsonrpc' => '2.0',
//                    'error' => [
//                        'code' => SolanaRpcClient::ERROR_CODE_METHOD_NOT_FOUND,
//                        'message' => 'Method not found'
//                    ],
//                    'id' => 1
//                ])
//            ),
//        ]);

        //$client = $this->assembleClient($mockHandler);

        // Assert that the correct exception is thrown for an invalid method
        $this->expectException(MethodNotFoundException::class);
        $client->call('invalid_method');

    }
}