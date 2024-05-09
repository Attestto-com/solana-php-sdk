<?php
namespace Attestto\SolanaPhpSdk\Tests\Unit\Programs\SNS;

use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Attestto\SolanaPhpSdk\Exceptions\InputValidationException;
use Attestto\SolanaPhpSdk\Exceptions\SNSError;
use Attestto\SolanaPhpSdk\Programs\SnsProgram;
use Attestto\SolanaPhpSdk\SolanaRpcClient;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\Util\Buffer;
use PHPUnit\Framework\MockObject\Exception;

class BindingsTest extends TestCase
{
    /**
     * @throws InputValidationException
     * @throws Exception
     */
    #[Test]
    public function testCreateSubDomain()
    {
        // Arrange

        $nameOwnerKey = new PublicKey(Buffer::alloc(32));


        $client = $this->createMock(SolanaRpcClient::class);
        $connection = $this->createMock(Connection::class);
        $sns = new SnsProgram($client);
        try {
            $instruction = $sns->createSubdomain(
                $connection,
                'subdomain',
                $nameOwnerKey,
                2000,
                $nameOwnerKey
            );
        } catch (AccountNotFoundException|SNSError $e) {
            $this->fail($e->getMessage());
        }

        // Assert
        $this->assertInstanceOf(TransactionInstruction::class, $instruction);
        $this->assertEquals(0, $instruction->data->toArray()[0]);

    }

    #[Test]
    public function test_createNameRegistry()
    {
        // Arrange
        $nameOwnerSigner = new PublicKey(Buffer::alloc(32));

        $client = $this->createMock(SolanaRpcClient::class);
        $connection = $this->createMock(Connection::class);
        $sns = new SnsProgram($client);


        $instruction = $sns->createNameRegistry(
            $connection,
            'domain',
            2000,
            $nameOwnerSigner,
            $nameOwnerSigner, // could be someone else
            null, null, null
        );

        // Assert
        $this->assertInstanceOf(TransactionInstruction::class, $instruction);
        $this->assertEquals(0, $instruction->data->toArray()[0]);
    }


}
