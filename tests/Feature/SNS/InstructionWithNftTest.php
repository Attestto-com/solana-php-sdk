<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit\Programs\SNS;

use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\Util\Buffer;
use Attestto\SolanaPhpSdk\Borsh\Borsh;
use Attestto\SolanaPhpSdk\Programs\SNS\CreateWithNftInstruction;

class CreateWithNftInstructionTest extends TestCase
{
    /** @test */
    public function it_serializes_instruction()
    {
        $obj = [
            'name' => 'Test Name',
            'space' => 123,
        ];

        $instruction = new CreateWithNftInstruction($obj);
        $serialized = $instruction->serialize();

        $this->assertInstanceOf(Buffer::class, $serialized);
        $this->assertNotEmpty($serialized->toBase58());
    }

    /** @test */
    public function it_creates_transaction_instruction()
    {
        $obj = [
            'name' => 'Test Name',
            'space' => 123,
        ];

        $programId = new PublicKey('program_id');
        $namingServiceProgram = new PublicKey('naming_service_program');
        $rootDomain = new PublicKey('root_domain');
        $name = new PublicKey('name');
        $reverseLookup = new PublicKey('reverse_lookup');
        $systemProgram = new PublicKey('system_program');
        $centralState = new PublicKey('central_state');
        $buyer = new PublicKey('buyer');
        $nftSource = new PublicKey('nft_source');
        $nftMetadata = new PublicKey('nft_metadata');
        $nftMint = new PublicKey('nft_mint');
        $masterEdition = new PublicKey('master_edition');
        $collection = new PublicKey('collection');
        $splTokenProgram = new PublicKey('spl_token_program');
        $rentSysvar = new PublicKey('rent_sysvar');
        $state = new PublicKey('state');
        $mplTokenMetadata = new PublicKey('mpl_token_metadata');

        $instruction = new CreateWithNftInstruction($obj);
        $transactionInstruction = $instruction->getInstruction(
            $programId,
            $namingServiceProgram,
            $rootDomain,
            $name,
            $reverseLookup,
            $systemProgram,
            $centralState,
            $buyer,
            $nftSource,
            $nftMetadata,
            $nftMint,
            $masterEdition,
            $collection,
            $splTokenProgram,
            $rentSysvar,
            $state,
            $mplTokenMetadata
        );

        $this->assertInstanceOf(TransactionInstruction::class, $transactionInstruction);
        $this->assertEquals($programId, $transactionInstruction->programId);
        $this->assertNotEmpty($transactionInstruction->data);
        $this->assertCount(17, $transactionInstruction->keys);
    }
}