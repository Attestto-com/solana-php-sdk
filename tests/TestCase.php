<?php

namespace Attestto\SolanaPhpSdk\Tests;

use Attestto\SolanaPhpSdk\SolanaRpcClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Orchestra\Testbench\TestCase as Orchestra;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Random\RandomException;

class TestCase extends Orchestra
{
    public mixed $config; // Holds the SDK config
    public string $endpoint = 'https://api.devnet.solana.com';

    public function setUp(): void
    {
        $jsonFilePath = dirname(__DIR__) . '/src/Programs/SNS/Constants/config.json';
        $this->config = json_decode(file_get_contents($jsonFilePath), true);
    }

    /**
     * @throws Exception
     * @throws RandomException
     */
    public function assembleClient(string $rpc_method, array $rpc_params): SolanaRpcClient
    {

        $client = new SolanaRpcClient(
            SolanaRpcClient::DEVNET_ENDPOINT
        );
        $rpc1 = $client->buildRpc($rpc_method, $rpc_params);
        $mockHandler = new MockHandler([
            new Response(
                200,
                [],
                json_encode($rpc1)
            ),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        // Create Guzzle HTTP client with handler stack
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);
        // Mock the request, stream, and URI factories
        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $uriFactory = $this->createMock(UriFactoryInterface::class);

        // Configure the request factory mock to return a request object
        $request = $this->createMock(RequestInterface::class);

        $requestFactory->method('createRequest')->willReturn($request);

        $client->httpClient = $guzzleClient;
        $client->requestFactory = $requestFactory;
        $client->streamFactory = $streamFactory;
        $client->uriFactory = $uriFactory;

        // Create an instance of SolanaRpcClient with the mocked dependencies
//        $client = new SolanaRpcClient(
//            SolanaRpcClient::DEVNET_ENDPOINT,
//            $guzzleClient,
//            $requestFactory,
//            $streamFactory,
//            $uriFactory
//        );

        return $client;
    }
}
