<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit\Programs\SNS;

use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\Util\Buffer;
use Attestto\SolanaPhpSdk\Borsh\Borsh;
use Attestto\SolanaPhpSdk\Programs\SNS\InstructionBurn;
use Attestto\SolanaPhpSdk\Programs\SNS\Utils;

class BurnInstructionTest extends TestCase
{

    // Load Constants/config.json
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_serializes_instruction()
    {
        $instruction = new InstructionBurn();
        $serialized = $instruction->serialize();
       
        $this->assertInstanceOf(Buffer::class, $serialized);
        
        $this->assertNotEmpty($serialized);
    }

    /** @test */
    public function it_creates_burn_instruction()
    {
        //=dd($this->config);
        $programId = new PublicKey($this->config['REGISTER_PROGRAM_ID']);
        $nameServiceId = new PublicKey($this->config['NAME_PROGRAM_ID']);
        $systemProgram = new PublicKey($this->config['SYSTEM_PROGRAM_ID']);
        $domain = '1automotive';
        $reverse = new PublicKey('reverse');
        $resellingState = new PublicKey('reselling_state');
        $domainPubkey = Utils::getDomainKeySync(
            new PublicKey('3Wnd5Df69KitZfUoPYZU438eFRNwGHkhLnSAWL65PxJX'),
            $domain,
            new PublicKey('3Wnd5Df69KitZfUoPYZU438eFRNwGHkhLnSAWL65PxJX'),
          );
        $state = PublicKey::findProgramAddressSync(
            [pubkey.toBuffer()],
            REGISTER_PROGRAM_ID,
          );
        $centralState = new PublicKey('central_state');
        $owner = new PublicKey('3Wnd5Df69KitZfUoPYZU438eFRNwGHkhLnSAWL65PxJX');
        $target = new PublicKey('3Wnd5Df69KitZfUoPYZU438eFRNwGHkhLnSAWL65PxJX');

        $instruction = new InstructionBurn();
        $transactionInstruction = $instruction->getInstruction(
            $programId,
            $nameServiceId,
            $systemProgram,
            $domain,
            $reverse,
            $resellingState,
            $state,
            $centralState,
            $owner,
            $target
        );

        $this->assertInstanceOf(TransactionInstruction::class, $transactionInstruction);
        $this->assertEquals($programId, $transactionInstruction->programId);
        $this->assertNotEmpty($transactionInstruction->data);
        $this->assertCount(9, $transactionInstruction->keys);
    }
}