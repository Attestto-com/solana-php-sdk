<?php

namespace Attestto\SolanaPhpSdk;

use GuzzleHttp\Psr7\Message;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;


use Attestto\SolanaPhpSdk\Exceptions\GenericException;
use Attestto\SolanaPhpSdk\Exceptions\InvalidIdResponseException;
use Attestto\SolanaPhpSdk\Exceptions\MethodNotFoundException;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Random\RandomException;


/**
 * @see https://docs.solana.com/developing/clients/jsonrpc-api
 */
class SolanaRpcClient
{
    public const LOCAL_ENDPOINT = 'http://localhost:8899';
    public const DEVNET_ENDPOINT = 'https://api.devnet.solana.com';
    public const TESTNET_ENDPOINT = 'https://api.testnet.solana.com';
    public const MAINNET_ENDPOINT = 'https://api.mainnet-beta.solana.com';

    /**
     * Per: https://www.jsonrpc.org/specification
     */
    // Invalid JSON was received by the server.
    // An error occurred on the server while parsing the JSON text.
    public const ERROR_CODE_PARSE_ERROR = -32700;
    // The JSON sent is not a valid Request object.
    public const ERROR_CODE_INVALID_REQUEST = -32600;
    // The method does not exist / is not available.
    public const ERROR_CODE_METHOD_NOT_FOUND = -32601;
    // Invalid method parameter(s).
    public const ERROR_CODE_INVALID_PARAMETERS = -32602;
    // Internal JSON-RPC error.
    public const ERROR_CODE_INTERNAL_ERROR = -32603;
    // Reserved for implementation-defined server-errors.
    // -32000 to -32099 is server error - no const.

    protected string $endpoint;
    protected int $randomKey;
    // Allows for dependency injection
    public ClientInterface $httpClient;
    public RequestFactoryInterface $requestFactory;
    public StreamFactoryInterface $streamFactory;
    public UriFactoryInterface $uriFactory;

    /**
     * @param string $endpoint
     * @param ClientInterface|null $httpClient
     * @param RequestFactoryInterface|null $requestFactory
     * @param StreamFactoryInterface|Message|null $streamFactory
     * @param UriFactoryInterface|null $uriFactory
     * @throws RandomException
     */
    public function __construct(
        string $endpoint,
        ClientInterface $httpClient = null,
        RequestFactoryInterface $requestFactory = null,
        StreamFactoryInterface|Message $streamFactory= null ,
        UriFactoryInterface $uriFactory= null
    ) {
        $this->endpoint = $endpoint ? : self::DEVNET_ENDPOINT;
        $this->randomKey = random_int(0, 99999999);
        $this->httpClient = $httpClient?: new GuzzleClient();
        $this->requestFactory = $requestFactory?: new HttpFactory();

    }

    /**
     * @param string $method
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws GenericException
     * @throws InvalidIdResponseException
     * @throws MethodNotFoundException|ClientExceptionInterface
     */
    public function call(string $method, array $params = [], array $headers = []): mixed
    {

        $body = json_encode($this->buildRpc($method, $params));
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'body' => $body,

        ];
        $response = $this->httpClient->request('POST', $this->endpoint, $options);

        $resp_body = $response->getBody()->getContents();
        $resp_object = json_decode($resp_body, true);

        $this->validateResponse($resp_object, $method);


        return $resp_object['result'] ?? null;
    }
    /**
     * @param string $method
     * @param array $params
     * @return array
     */
    public function buildRpc(string $method, array $params): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $this->randomKey,
            'method' => $method,
            'params' => $params,

        ];
    }

    /**
     * @param mixed $response
     * @param string $method
     * @throws GenericException
     * @throws InvalidIdResponseException
     * @throws MethodNotFoundException
     */
    protected function validateResponse(array $body, string $method): void
    {

        // Get response body as string
        //$body = $response->getBody()->getContents();

        // Decode JSON response body
        //$resp = json_decode($body, true);



        if ($body == null) {
            throw new GenericException('Invalid JSON response');
        }

        // If response contains an 'error' key, handle it
        if (isset($body['params']['error']) || isset($body['error'])) {
            $error = $body['params']['error']? : $body['error'];
            if ($error['code'] === self::ERROR_CODE_METHOD_NOT_FOUND) {
                throw new MethodNotFoundException("API Error: Method $method not found.");
            } else {
                throw new GenericException($error['message']);
            }
        }

        // If 'id' doesn't match the expected value, throw an exception
        if ($body['id'] !== $this->randomKey) {
            throw new InvalidIdResponseException($this->randomKey);
        }


    }

    /**
     * @return int
     */
    public function getRandomKey(): int
    {
        return $this->randomKey;
    }
}
