<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit\Programs;

use Attestto\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Attestto\SolanaPhpSdk\Programs\SystemProgram;
use Attestto\SolanaPhpSdk\SolanaRpcClient;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;


class SolanaTest extends TestCase
{

    private SystemProgram $program;


    public function setUp(): void
    {
        $client = $this->createMock(SolanaRpcClient::class);
        $this->program = new SystemProgram($client);
    }


    /**
     * @throws AccountNotFoundException
     */
    #[Test]
    public function test_it_will_throw_exception_when_rpc_account_response_is_null(): void
    {

        $client = $this->assembleClient('POST', []);

        $solana = new SystemProgram($client);

        $this->expectException(AccountNotFoundException::class);
        $solana->getAccountInfo('abc123');
    }

    #[Test]
    public function testConfig()
    {
        $key = 'TOKEN_PROGRAM_ID';
        $value = 'TokenkegQfeZyiNwAJbNbGKPFXCWuBvf9Ss623VQ5DA';

        $this->setUp();
        $config = $this->program->config($key);
        $this->assertEquals($value, $config);
    }


}
