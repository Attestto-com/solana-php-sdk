<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit\Programs;

use Attestto\SolanaPhpSdk\Programs\CreateV2Instruction;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\SystemProgram;
use Attestto\SolanaPhpSdk\TokenProgramId;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use PHPUnit\Framework\TestCase;

class CreateV2InstructionTest extends TestCase
{
    public function testSerialize()
    {
        $instruction = new CreateV2Instruction([
            'name' => 'Test Instruction',
            'space' => 100,
        ]);

        $buffer = $instruction->serialize();

        // Add your assertions for the serialized buffer here
    }

    public function testGetInstruction()
    {
        $instruction = new CreateV2Instruction([
            'name' => 'Test Instruction',
            'space' => 100,
        ]);

        $programId = new PublicKey('program_id');
        $rentSysvarAccount = new PublicKey('rent_sysvar_account');
        $nameProgramId = new PublicKey('name_program_id');
        $rootDomain = new PublicKey('root_domain');
        $nameAccount = new PublicKey('name_account');
        $reverseLookupAccount = new PublicKey('reverse_lookup_account');
        $centralState = new PublicKey('central_state');
        $buyer = new PublicKey('buyer');
        $buyerTokenAccount = new PublicKey('buyer_token_account');
        $usdcVault = new PublicKey('usdc_vault');
        $state = new PublicKey('state');

        $transactionInstruction = $instruction->getInstruction(
            $programId,
            $rentSysvarAccount,
            $nameProgramId,
            $rootDomain,
            $nameAccount,
            $reverseLookupAccount,
            $centralState,
            $buyer,
            $buyerTokenAccount,
            $usdcVault,
            $state
        );

        // Add your assertions for the transaction instruction here
    }
}