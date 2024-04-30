<?php

namespace Attestto\SolanaPhpSdk\Tests;

use Attestto\SolanaPhpSdk\SolanaRpcClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Orchestra\Testbench\TestCase as Orchestra;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Random\RandomException;

class TestCase extends Orchestra
{
    public $config;

    public function setUp(): void
    {
        $jsonFilePath = dirname(__DIR__) . '/src/Programs/SNS/Constants/config.json';
       $this->config = json_decode(file_get_contents($jsonFilePath), true);
    }

    /**
     * @throws Exception
     * @throws RandomException
     */
    public function assembleClient($mockHandler): SolanaRpcClient
    {
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
        // Create an instance of SolanaRpcClient with the mocked dependencies
        $client = new SolanaRpcClient(
            SolanaRpcClient::DEVNET_ENDPOINT,
            $guzzleClient,
            $requestFactory,
            $streamFactory,
            $uriFactory
        );

        return $client;
    }
}
