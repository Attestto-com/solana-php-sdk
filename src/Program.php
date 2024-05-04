<?php

namespace Attestto\SolanaPhpSdk;

class Program
{
    /**
     * @var SolanaRpcClient
     */
    protected SolanaRpcClient $client;
    protected mixed $config;

    public function __construct(SolanaRpcClient $client)
    {
        $this->client = $client;
        $this->config = require __DIR__ . '/../config/solana-sdk.php';
    }

//    public function __call($method, $params = [])
//    {
//        return $this->client->call($method, ...$params);
//    }

    public function config(string $key)
    {
        return $this->config[$key] ?? null;
    }
}
