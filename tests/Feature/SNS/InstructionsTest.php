<?php

namespace Attestto\SolanaPhpSdk\Tests;

use Attestto\SolanaPhpSdk\Util\Buffer;
use Attestto\SolanaPhpSdk\Numberu32;
use Attestto\SolanaPhpSdk\Numberu64;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\SnsInstruction;
use Attestto\SolanaPhpSdk\SystemProgram;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\Tests\TestCase;

class SnsInstructionTest extends TestCase
{
    /** @test */
    public function it_creates_instruction_create_with_all_parameters()
    {
        $nameProgramId = new PublicKey('name_program_id');
        $systemProgramId = new PublicKey('system_program_id');
        $nameKey = new PublicKey('name_key');
        $nameOwnerKey = new PublicKey('name_owner_key');
        $payerKey = new PublicKey('payer_key');
        $hashedName = new Buffer('hashed_name');
        $lamports = new Numberu64(1000);
        $space = new Numberu32(100);
        $nameClassKey = new PublicKey('name_class_key');
        $nameParent = new PublicKey('name_parent');
        $nameParentOwner = new PublicKey('name_parent_owner');

        $snsInstruction = new SnsInstruction();
        $instruction = $snsInstruction->createInstruction(
            $nameProgramId,
            $systemProgramId,
            $nameKey,
            $nameOwnerKey,
            $payerKey,
            $hashedName,
            $lamports,
            $space,
            $nameClassKey,
            $nameParent,
            $nameParentOwner
        );

        $this->assertInstanceOf(TransactionInstruction::class, $instruction);
        $this->assertEquals($nameProgramId, $instruction->getProgramId());
        $this->assertEquals([
            [
                'pubkey' => $systemProgramId,
                'isSigner' => false,
                'isWritable' => false
            ],
            [
                'pubkey' => $payerKey,
                'isSigner' => true,
                'isWritable' => true
            ],
            [
                'pubkey' => $nameKey,
                'isSigner' => false,
                'isWritable' => true
            ],
            [
                'pubkey' => $nameOwnerKey,
                'isSigner' => false,
                'isWritable' => false
            ],
            [
                'pubkey' => $nameClassKey,
                'isSigner' => true,
                'isWritable' => false
            ],
            [
                'pubkey' => $nameParent,
                'isSigner' => false,
                'isWritable' => false
            ],
            [
                'pubkey' => $nameParentOwner,
                'isSigner' => true,
                'isWritable' => false
            ]
        ], $instruction->getKeys());
        $this->assertEquals($hashedName, $instruction->getData());
    }

    /** @test */
    public function it_creates_instruction_create_with_optional_parameters_null()
    {
        $nameProgramId = new PublicKey('name_program_id');
        $systemProgramId = new PublicKey('system_program_id');
        $nameKey = new PublicKey('name_key');
        $nameOwnerKey = new PublicKey('name_owner_key');
        $payerKey = new PublicKey('payer_key');
        $hashedName = new Buffer('hashed_name');
        $lamports = new Numberu64(1000);
        $space = new Numberu32(100);
        $nameClassKey = null;
        $nameParent = null;
        $nameParentOwner = null;

        $snsInstruction = new SnsInstruction();
        $instruction = $snsInstruction->createInstruction(
            $nameProgramId,
            $systemProgramId,
            $nameKey,
            $nameOwnerKey,
            $payerKey,
            $hashedName,
            $lamports,
            $space,
            $nameClassKey,
            $nameParent,
            $nameParentOwner
        );

        $this->assertInstanceOf(TransactionInstruction::class, $instruction);
        $this->assertEquals($nameProgramId, $instruction->getProgramId());
        $this->assertEquals([
            [
                'pubkey' => $systemProgramId,
                'isSigner' => false,
                'isWritable' => false
            ],
            [
                'pubkey' => $payerKey,
                'isSigner' => true,
                'isWritable' => true
            ],
            [
                'pubkey' => $nameKey,
                'isSigner' => false,
                'isWritable' => true
            ],
            [
                'pubkey' => $nameOwnerKey,
                'isSigner' => false,
                'isWritable' => false
            ],
            [
                'pubkey' => new PublicKey(Buffer::alloc(32)),
                'isSigner' => false,
                'isWritable' => false
            ],
            [
                'pubkey' => new PublicKey(Buffer::alloc(32)),
                'isSigner' => false,
                'isWritable' => false
            ],
            [
                'pubkey' => new PublicKey(Buffer::alloc(32)),
                'isSigner' => false,
                'isWritable' => false
            ]
        ], $instruction->getKeys());
        $this->assertEquals($hashedName, $instruction->getData());
    }

    /** @test */
    public function it_creates_instruction_update()
    {
        $nameProgramId = new PublicKey('name_program_id');
        $nameAccountKey = new PublicKey('name_account_key');
        $offset = new Numberu32(10);
        $inputData = new Buffer('input_data');
        $nameUpdateSigner = new PublicKey('name_update_signer');

        $snsInstruction = new SnsInstruction();
        $instruction = $snsInstruction->updateInstruction(
            $nameProgramId,
            $nameAccountKey,
            $offset,
            $inputData,
            $nameUpdateSigner
        );

        $this->assertInstanceOf(TransactionInstruction::class, $instruction);
        $this->assertEquals($nameProgramId, $instruction->getProgramId());
        $this->assertEquals([
            [
                'pubkey' => $nameAccountKey,
                'isSigner' => false,
                'isWritable' => true
            ],
            [
                'pubkey' => $nameUpdateSigner,
                'isSigner' => true,
                'isWritable' => false
            ]
        ], $instruction->getKeys());
        $this->assertEquals(
            Buffer::concat([
                Buffer::fromArray([1]),
                $offset->toBuffer(),
                (new Numberu32($inputData->length))->toBuffer(),
                $inputData
            ]),
            $instruction->getData()
        );
    }

    /** @test */
    public function it_creates_transfer_instruction_with_all_parameters()
    {
        $nameProgramId = new PublicKey('name_program_id');
        $nameAccountKey = new PublicKey('name_account_key');
        $newOwnerKey = new PublicKey('new_owner_key');
        $currentNameOwnerKey = new PublicKey('current_name_owner_key');
        $nameClassKey = new PublicKey('name_class_key');
        $nameParent = new PublicKey('name_parent');
        $parentOwner = new PublicKey('parent_owner');

        $snsInstruction = new SnsInstruction();
        $instruction = $snsInstruction->transferInstruction(
            $nameProgramId,
            $nameAccountKey,
            $newOwnerKey,
            $currentNameOwnerKey,
            $nameClassKey,
            $nameParent,
            $parentOwner
        );

        $this->assertInstanceOf(TransactionInstruction::class, $instruction);
        $this->assertEquals($nameProgramId, $instruction->getProgramId());
        $this->assertEquals([
            [
                'pubkey' => $nameAccountKey,
                'isSigner' => false,
                'isWritable' => true
            ],
            [
                'pubkey' => $parentOwner,
                'isSigner' => true,
                'isWritable' => false
            ],
            [
                'pubkey' => $nameClassKey,
                'isSigner' => true,
                'isWritable' => false
            ],
            [
                'pubkey' => $nameParent,
                'isSigner' => false,
                'isWritable' => false
            ]
        ], $instruction->getKeys());
        $this->assertEquals(
            Buffer::concat([
                Buffer::fromArray([2]),
                $newOwnerKey->toBuffer()
            ]),
            $instruction->getData()
        );
    }

    /** @test */
    public function it_creates_transfer_instruction_with_optional_parameters_null()
    {
        $nameProgramId = new PublicKey('name_program_id');
        $nameAccountKey = new PublicKey('name_account_key');
        $newOwnerKey = new PublicKey('new_owner_key');
        $currentNameOwnerKey = new PublicKey('current_name_owner_key');
        $nameClassKey = null;
        $nameParent = null;
        $parentOwner = null;

        $snsInstruction = new SnsInstruction();
        $instruction = $snsInstruction->transferInstruction(
            $nameProgramId,
            $nameAccountKey,
            $newOwnerKey,
            $currentNameOwnerKey,
            $nameClassKey,
            $nameParent,
            $parentOwner
        );

        $this->assertInstanceOf(TransactionInstruction::class, $instruction);
        $this->assertEquals($nameProgramId, $instruction->getProgramId());
        $this->assertEquals([
            [
                'pubkey' => $nameAccountKey,
                'isSigner' => false,
                'isWritable' => true
            ],
            [
                'pubkey' => $currentNameOwnerKey,
                'isSigner' => true,
                'isWritable' => false
            ]
        ], $instruction->getKeys());
        $this->assertEquals(
            Buffer::concat([
                Buffer::fromArray([2]),
                $newOwnerKey->toBuffer()
            ]),
            $instruction->getData()
        );
    }

    /** @test */
    public function it_creates_realloc_instruction()
    {
        $nameProgramId = new PublicKey('name_program_id');
        $systemProgramId = new PublicKey('system_program_id');
        $payerKey = new PublicKey('payer_key');
        $nameAccountKey = new PublicKey('name_account_key');
        $nameOwnerKey = new PublicKey('name_owner_key');
        $space = new Numberu32(100);

        $snsInstruction = new SnsInstruction();
        $instruction = $snsInstruction->reallocInstruction(
            $nameProgramId,
            $systemProgramId,
            $payerKey,
            $nameAccountKey,
            $nameOwnerKey,
            $space
        );

        $this->assertInstanceOf(TransactionInstruction::class, $instruction);
        $this->assertEquals($nameProgramId, $instruction->getProgramId());
        $this->assertEquals([
            [
                'pubkey' => $systemProgramId,
                'isSigner' => false,
                'isWritable' => false
            ],
            [
                'pubkey' => $payerKey,
                'isSigner' => true,
                'isWritable' => true
            ],
            [
                'pubkey' => $nameAccountKey,
                'isSigner' => false,
                'isWritable' => true
            ],
            [
                'pubkey' => $nameOwnerKey,
                'isSigner' => true,
                'isWritable' => false
            ]
        ], $instruction->getKeys());
        $this->assertEquals(
            Buffer::concat([
                Buffer::fromArray([4]),
                $space->toBuffer()
            ]),
            $instruction->getData()
        );
    }

    /** @test */
    public function it_creates_delete_instruction()
    {
        $nameProgramId = new PublicKey('name_program_id');
        $nameAccountKey = new PublicKey('name_account_key');
        $refundTargetKey = new PublicKey('refund_target_key');
        $nameOwnerKey = new PublicKey('name_owner_key');

        $snsInstruction = new SnsInstruction();
        $instruction = $snsInstruction->deleteInstruction(
            $nameProgramId,
            $nameAccountKey,
            $refundTargetKey,
            $nameOwnerKey
        );

        $this->assertInstanceOf(TransactionInstruction::class, $instruction);
        $this->assertEquals($nameProgramId, $instruction->getProgramId());
        $this->assertEquals([
            [
                'pubkey' => $nameAccountKey,
                'isSigner' => false,
                'isWritable' => true
            ],
            [
                'pubkey' => $nameOwnerKey,
                'isSigner' => true,
                'isWritable' => false
            ],
            [
                'pubkey' => $refundTargetKey,
                'isSigner' => false,
                'isWritable' => true
            ]
        ], $instruction->getKeys());
        $this->assertEquals(
            Buffer::fromArray([3]),
            $instruction->getData()
        );
    }
}