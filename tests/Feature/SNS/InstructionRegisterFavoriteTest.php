<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit\Programs\SNS;

use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\Util\Buffer;
use Attestto\SolanaPhpSdk\Borsh\Borsh;
use Attestto\SolanaPhpSdk\Programs\SNS\RegisterFavoriteInstruction;

class RegisterFavoriteInstructionTest extends TestCase
{
    /** @test */
    public function it_serializes_instruction()
    {
        $instruction = new RegisterFavoriteInstruction();
        $serialized = $instruction->serialize();

        $this->assertInstanceOf(Buffer::class, $serialized);
        $this->assertNotEmpty($serialized->toBase58());
    }

    /** @test */
    public function it_creates_transaction_instruction()
    {
        $programId = new PublicKey('program_id');
        $nameAccount = new PublicKey('name_account');
        $favouriteAccount = new PublicKey('favourite_account');
        $owner = new PublicKey('owner');
        $systemProgram = new PublicKey('system_program');

        $instruction = new RegisterFavoriteInstruction();
        $transactionInstruction = $instruction->getInstruction(
            $programId,
            $nameAccount,
            $favouriteAccount,
            $owner,
            $systemProgram
        );

        $this->assertInstanceOf(TransactionInstruction::class, $transactionInstruction);
        $this->assertEquals($programId, $transactionInstruction->programId);
        $this->assertNotEmpty($transactionInstruction->data);
        $this->assertCount(4, $transactionInstruction->keys);
    }
}