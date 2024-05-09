<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit;

use Attestto\SolanaPhpSdk\Account;
use Attestto\SolanaPhpSdk\Exceptions\InputValidationException;
use Attestto\SolanaPhpSdk\Keypair;
use Attestto\SolanaPhpSdk\Programs\SystemProgram;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use Attestto\SolanaPhpSdk\Util\Buffer;

class BufferTest extends TestCase
{
    #[Test]
    public function test_it_buffer_push_fixed_length()
    {
        $lamports = 4;
        $space = 6;
        $programId = Keypair::generate()->getPublicKey();

        $rawCreateAccountBinary = [
            // uint32
            ...unpack("C*", pack("V", SystemProgram::PROGRAM_INDEX_CREATE_ACCOUNT)),
            // int64
            ...unpack("C*", pack("P", $lamports)),
            // int64
            ...unpack("C*", pack("P", $space)),
            //
            ...$programId->toBytes(),
        ];

        $bufferable = Buffer::from()
            ->push(
                Buffer::from(SystemProgram::PROGRAM_INDEX_CREATE_ACCOUNT,Buffer::TYPE_INT, false)
            )
            ->push(
                Buffer::from($lamports,Buffer::TYPE_LONG, false)
            )
            ->push(
                Buffer::from($space,Buffer::TYPE_LONG, false)
            )
            ->push($programId)
        ;

        $this->assertEquals($rawCreateAccountBinary, $bufferable->toArray());
    }

    /**
     * @throws InputValidationException
     */
    #[Test]
    public function test_concat()
    {
        $buffer1 = Buffer::from(1, Buffer::TYPE_INT, false);
        $buffer2 = Buffer::from(2, Buffer::TYPE_INT, false);
        $buffer3 = Buffer::from(3, Buffer::TYPE_INT, false);

        $buffer = Buffer::concat([$buffer1, $buffer2, $buffer3]);

        $this->assertEquals([1, 0, 0, 0, 2, 0, 0, 0, 3, 0, 0, 0], $buffer->toArray());
    }

    /**
     * @throws InputValidationException
     */
    #[Test]
    public function test_fromArray()
    {
        $buffer = Buffer::fromArray([1, 2, 3, 4]);
        $this->assertEquals([1, 2, 3, 4], $buffer->toArray());
    }
}
