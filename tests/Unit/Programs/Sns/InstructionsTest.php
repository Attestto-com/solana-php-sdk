<?php
namespace Attestto\SolanaPhpSdk\Tests\Unit\Programs\SNS;

use Attestto\SolanaPhpSdk\Exceptions\InputValidationException;
use Attestto\SolanaPhpSdk\Programs\SnsProgram;
use Attestto\SolanaPhpSdk\SolanaRpcClient;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\Util\Buffer;
use PHPUnit\Framework\MockObject\Exception;

class InstructionsTest extends TestCase
{
    /**
     * @throws InputValidationException
     * @throws Exception
     */
    #[Test]
    public function testCreateInstruction()
    {
        // Arrange
        $nameProgramId = new PublicKey(Buffer::alloc(32));
        $systemProgramId = new PublicKey(Buffer::alloc(32));
        $nameKey = new PublicKey(Buffer::alloc(32));
        $nameOwnerKey = new PublicKey(Buffer::alloc(32));
        $payerKey = new PublicKey(Buffer::alloc(32));
        $hashed_name = new Buffer(32, Buffer::TYPE_INT, false);
        $lamports = new Buffer(1000000, Buffer::TYPE_INT, false);
        $space = new Buffer(2000, Buffer::TYPE_INT, false);
        $nameClassKey = new PublicKey(Buffer::alloc(32));
        $nameParent = new PublicKey(Buffer::alloc(32));
        $nameParentOwner = new PublicKey(Buffer::alloc(32));

        $client = $this->createMock(SolanaRpcClient::class);

        $sns = new SnsProgram($client);
        $instruction = $sns->createInstruction(
            $nameProgramId,
            $systemProgramId,
            $nameKey,
            $nameOwnerKey,
            $payerKey,
            $hashed_name,
            $lamports,
            $space,
            $nameClassKey,
            $nameParent,
            $nameParentOwner
        );

        // Assert
        $this->assertInstanceOf(TransactionInstruction::class, $instruction);
        $this->assertEquals(0, $instruction->data->toArray()[0]);

        // TODO: Add more assertions here to verify the properties of the returned TransactionInstruction
    }

    #[Test]
    public function test_updateInstruction()
    {
        // Arrange
        $nameProgramId = new PublicKey(Buffer::alloc(32));
        $nameAccountKey = new PublicKey(Buffer::alloc(32));
        $offset = new Buffer(96, Buffer::TYPE_INT, false);
        $inputData = new Buffer('INPUT DATA', );
        $nameUpdateSigner = new PublicKey(Buffer::alloc(32));

        $client = $this->createMock(SolanaRpcClient::class);

        $sns = new SnsProgram($client);
        $instruction = $sns->updateInstruction(
            $nameProgramId,
            $nameAccountKey,
            $offset,
            $inputData,
            $nameUpdateSigner
        );

        // Assert
        $this->assertInstanceOf(TransactionInstruction::class, $instruction);
        $this->assertEquals(1, $instruction->data->toArray()[0]);
    }

    #[Test]
    public function test_transferInstruction()
    {
        // Arrange
        $nameProgramId = new PublicKey(Buffer::alloc(32));
        $nameAccountKey = new PublicKey(Buffer::alloc(32));
        $newOwnerKey = new PublicKey(Buffer::alloc(32));
        $currentNameOwnerKey = new PublicKey(Buffer::alloc(32));


        $client = $this->createMock(SolanaRpcClient::class);

        $sns = new SnsProgram($client);
        $instruction = $sns->transferInstruction(
            $nameProgramId,
            $nameAccountKey,
            $newOwnerKey,
            $currentNameOwnerKey,
            null, null, null
        );

        // Assert
        $this->assertInstanceOf(TransactionInstruction::class, $instruction);
        $this->assertEquals(2, $instruction->data->toArray()[0]);
    }

    #[Test]
    public function test_reallocInstruction()
    {
        // Arrange
        $nameProgramId = new PublicKey(Buffer::alloc(32));
        $systemProgramId = new PublicKey(Buffer::alloc(32));
        $nameAccountKey = new PublicKey(Buffer::alloc(32));
        $currentNameOwnerKey = new PublicKey(Buffer::alloc(32));
        $space = new Buffer(2000, Buffer::TYPE_INT, false);

        $client = $this->createMock(SolanaRpcClient::class);

        $sns = new SnsProgram($client);
        $instruction = $sns->reallocInstruction(
            $nameProgramId,
            $systemProgramId,
            $currentNameOwnerKey, // Payer
            $nameAccountKey,
            $currentNameOwnerKey,
            $space
        );

        // Assert
        $this->assertInstanceOf(TransactionInstruction::class, $instruction);
        $this->assertEquals(4, $instruction->data->toArray()[0]);
    }

    #[Test]
    public function test_deleteInstruction()
    {
        // Arrange
        $nameProgramId = new PublicKey(Buffer::alloc(32));

        $nameAccountKey = new PublicKey(Buffer::alloc(32));
        $currentNameOwnerKey = new PublicKey(Buffer::alloc(32));


        $client = $this->createMock(SolanaRpcClient::class);

        $sns = new SnsProgram($client);
        $instruction = $sns->deleteInstruction(
            $nameProgramId,
            $nameAccountKey,
            $currentNameOwnerKey, // Refund Target
            $currentNameOwnerKey
        );

        // Assert
        $this->assertInstanceOf(TransactionInstruction::class, $instruction);
        $this->assertEquals(3, $instruction->data->toArray()[0]);
    }

}
