<?php

namespace SplToken;

use Attestto\SolanaPhpSdk\Programs\SplTokenProgram;
use Attestto\SolanaPhpSdk\Tests\TestCase;

class SPLTokenTest extends TestCase
{
    private $splTokenProgram;

    public function setUp(): void
    {
        $client = $this->assembleClient('POST', []);
        $this->splTokenProgram = new SplTokenProgram($client);
    }

//    public function testGetAssociatedTokenAddressSync()
//    {
//        $mockMint = $this->createMock(PublicKey::class);
//        $mockOwner = $this->createMock(PublicKey::class);
//        $mockOwner->method('toBuffer')->willReturn('buffer');
//        $mockOwner->method('isOnCurve')->willReturn(true);
//
//        $mockProgramId = 'programId';
//        $mockAssociatedTokenProgramId = 'associatedTokenProgramId';
//
//        $result = $this->splToken->getAssociatedTokenAddressSync(
//            $mockMint,
//            $mockOwner,
//            false,
//            $mockProgramId,
//            $mockAssociatedTokenProgramId
//        );
//
//        $this->assertInstanceOf(PublicKey::class, $result);
//    }
//
//    public function testGetAssociatedTokenAddressSyncThrowsException()
//    {
//        $this->expectException(TokenOwnerOffCurveError::class);
//
//        $mockMint = $this->createMock(PublicKey::class);
//        $mockOwner = $this->createMock(PublicKey::class);
//        $mockOwner->method('toBuffer')->willReturn('buffer');
//        $mockOwner->method('isOnCurve')->willReturn(false);
//
//        $mockProgramId = 'programId';
//        $mockAssociatedTokenProgramId = 'associatedTokenProgramId';
//
//        $this->splToken->getAssociatedTokenAddressSync(
//            $mockMint,
//            $mockOwner,
//            false,
//            $mockProgramId,
//            $mockAssociatedTokenProgramId
//        );
//    }
}