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

        $mockHandler = new MockHandler([
            new Response(
                200,
                [],
                json_encode([
                    'jsonrpc' => '2.0',
                    'result' => [
                        'value' => [ 'data' => '...' ]
                    ],
                    'id' => 12345 // Set response ID different from the random key
                ])
            ),
        ]);

        $client = $this->assembleClient($mockHandler);

        $rpc1 = $client->buildRpc('doStuff', []);
        $rpc2 = $client->buildRpc('doStuff', []);

        $client = $this->assembleClient($mockHandler);

        $rpc3= $client->buildRpc('doStuff', []);
        $rpc4 = $client->buildRpc('doStuff', []);

        $this->assertEquals($rpc1['id'], $rpc2['id']);
        $this->assertEquals($rpc3['id'], $rpc4['id']);
        $this->assertNotEquals($rpc1['id'], $rpc4['id']);
    }

    /**
     * @throws MethodNotFoundException
     * @throws Exception
     * @throws RequestException
     * @throws GenericException
     * @throws \Exception
     */
    #[Test]
    public function test_it_validates_response_id()
    {
        $mockHandler = new MockHandler([
            new Response(
                200,
                [],
                json_encode([
                    'jsonrpc' => '2.0',
                    'result' => [
                        'value' => [ 'data' => '...' ]
                    ],
                    'id' => 12345 // Set response ID different from the random key
                ])
            ),
        ]);
        $client = $this->assembleClient($mockHandler);

        // Assert that the correct exception is thrown when the response ID is invalid
        $this->expectException(InvalidIdResponseException::class);
        $client->call('getAccountInfo');
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

        // Create mock handler for Guzzle
        $mockHandler = new MockHandler([
            new Response(
                200,
                [],
                json_encode([
                    'jsonrpc' => '2.0',
                    'error' => [
                        'code' => SolanaRpcClient::ERROR_CODE_METHOD_NOT_FOUND,
                        'message' => 'Method not found'
                    ],
                    'id' => 1
                ])
            ),
        ]);

        $client = $this->assembleClient($mockHandler);

        // Assert that the correct exception is thrown for an invalid method
        $this->expectException(MethodNotFoundException::class);
        $client->call('invalid_method');

    }
}