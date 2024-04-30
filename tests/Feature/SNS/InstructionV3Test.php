<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit;

use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use Attestto\SolanaPhpSdk\Programs\SNS\CreateInstructionV3;

class CreateInstructionV3Test extends TestCase
{
    /** @test */
    public function it_serializes_instruction()
    {
        $obj = [
            'name' => 'example',
            'space' => 100,
            'referrerIdxOpt' => 1,
        ];

        $instruction = new CreateInstructionV3($obj);
        $serialized = $instruction->serialize();

        $this->assertInstanceOf(Buffer::class, $serialized);
        // Add assertions to validate the serialized buffer
    }

    /** @test */
    public function it_creates_transaction_instruction()
    {
        $obj = [
            'name' => 'example',
            'space' => 100,
            'referrerIdxOpt' => 1,
        ];

        $instruction = new CreateInstructionV3($obj);

        $programId = new PublicKey('program_id');
        $namingServiceProgram = new PublicKey('naming_service_program');
        $rootDomain = new PublicKey('root_domain');
        $name = new PublicKey('name');
        $reverseLookup = new PublicKey('reverse_lookup');
        $systemProgram = new PublicKey('system_program');
        $centralState = new PublicKey('central_state');
        $buyer = new PublicKey('buyer');
        $buyerTokenSource = new PublicKey('buyer_token_source');
        $pythMappingAcc = new PublicKey('pyth_mapping_acc');
        $pythProductAcc = new PublicKey('pyth_product_acc');
        $pythPriceAcc = new PublicKey('pyth_price_acc');
        $vault = new PublicKey('vault');
        $splTokenProgram = new PublicKey('spl_token_program');
        $rentSysvar = new PublicKey('rent_sysvar');
        $state = new PublicKey('state');
        $referrerAccountOpt = new PublicKey('referrer_account_opt');

        $transactionInstruction = $instruction->getInstruction(
            $programId,
            $namingServiceProgram,
            $rootDomain,
            $name,
            $reverseLookup,
            $systemProgram,
            $centralState,
            $buyer,
            $buyerTokenSource,
            $pythMappingAcc,
            $pythProductAcc,
            $pythPriceAcc,
            $vault,
            $splTokenProgram,
            $rentSysvar,
            $state,
            $referrerAccountOpt
        );

        $this->assertInstanceOf(TransactionInstruction::class, $transactionInstruction);
        // Add assertions to validate the transaction instruction
    }
}