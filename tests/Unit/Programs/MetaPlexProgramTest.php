<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit\Programs;

use Attestto\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Attestto\SolanaPhpSdk\Programs\SystemProgram;
use Attestto\SolanaPhpSdk\SolanaRpcClient;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;


class MetaPlexProgramTest extends TestCase
{

    private SystemProgram $program;





    /**
     * @throws AccountNotFoundException
     */
    #[Test]
    public function test_it_getsProgramAccounts(): void
    {

        $client = $this->assembleClient('POST', []);

        $solana = new SystemProgram($client);

        $this->expectException(AccountNotFoundException::class);
        $solana->getAccountInfo('abc123');
    }



}
