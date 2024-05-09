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
    public function testCreateSubDomainFast()
    {
        // Arrange

        $nameOwnerKey = new PublicKey('6V3DAZhWgATw8hrmMh7DnvLgaVpHLuMafZZPTVnyUs6Y');


        $rpcClient = new SolanaRpcClient('https://api.mainnet-beta.solana.com');
        $connection = new Connection($rpcClient);
        $sns = new SnsProgram($rpcClient);

        $instruction = $sns->createSubdomainFast(
                $connection,
                'subdomain.chongkan.sol',
                new PublicKey('57vj6H1omWUvrQypM8esx4q67WNRZhTW3ZHZ97unkSTb'), // f.chongkan.sol
                new PublicKey('34MxBdMJYgugd9ZzmZN338kL1vMqkhPqtnZG5qmWnfn1'),
                $nameOwnerKey,
                1_000,
                $nameOwnerKey
            );


        // Assert
        $this->assertInstanceOf(TransactionInstruction::class, $instruction[1][0]);
        // TODO Assert IX keys and data

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
