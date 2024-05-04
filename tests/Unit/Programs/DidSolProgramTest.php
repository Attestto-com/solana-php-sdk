<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit\Programs;

use Attestto\SolanaPhpSdk\Programs\DidSolProgram;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DidSolProgramTest extends TestCase
{

    public const ACC_DATA_SIZE = 158;
    public const DID_ID = 'did:sol:devnet:3Js7k6xYQbvXv6qUYLapYV7Sptfg37Tss9GcAyVEuUqk';
    public const DID_SUBJECT_PK = '3Js7k6xYQbvXv6qUYLapYV7Sptfg37Tss9GcAyVEuUqk';
    public const DID_ACCOUNT_ID = '2LA5JTs1cxFewfnXzVBpaFHpABBj1akR2aQzwDSovwCg';
    public const DID_DATA = 'TVjvjfsd7fMA/gAAAAAAAAAABwAAAGRlZmF1bHRIAAAgAAAAIkrqC+g88eamANb3tU6OiBJW21IjBWP85MhI4XKkOscAAAAAAQAAAAUAAABhZ2VudAwAAABBZ2VudFNlcnZpY2UtAAAAaHR0cHM6Ly9hdHRlc3R0by1icmVlemUtdnVlLnRlc3QvLndlbGwta25vd24vAAAAAAAAAAA=';
    #[Test]
    public function test_it_deserializes_diddata()
    {
        $base64Data = self::DID_DATA;
        $didData = DidSolProgram::deserializeDidData($base64Data);

        $this->assertEquals(self::DID_SUBJECT_PK, $didData->keyData);

    }

    #[Test]
    public function test_it_gets_did_data_account_id()
    {

        $didId = DidSolProgram::getDidDataAccountId( self::DID_SUBJECT_PK,);
        $this->assertEquals(self::DID_ACCOUNT_ID, $didId);

    }
}
