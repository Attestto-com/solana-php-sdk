<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Attestto\SolanaPhpSdk\Exceptions\GenericException;
use Attestto\SolanaPhpSdk\Programs\SystemProgram;
use Attestto\SolanaPhpSdk\SolanaRpcClient;
use Attestto\SolanaPhpSdk\Tests\TestCase;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Util\AccountMeta;
use Attestto\SolanaPhpSdk\Util\Buffer;


class TransactionInstructionTest extends TestCase
{
    #[Test]
    public function test_it_creates_transaction_instruction_with_program_id_and_keys()
    {
        $programId = new PublicKey('3Wnd5Df69KitZfUoPYZU438eFRNwGHkhLnSAWL65PxJX');
        $pk2 = new PublicKey('3Js7k6xYQbvXv6qUYLapYV7Sptfg37Tss9GcAyVEuUqk');
        $keys = [
            new AccountMeta($programId, true, true),
            new AccountMeta($pk2, false, true),
        ];
        $data = 'some data';

        $instruction = new TransactionInstruction($programId, $keys, $data);

        $this->assertEquals($programId, $instruction->programId);
        $this->assertEquals($keys, $instruction->keys);
        $this->assertEquals($data, $instruction->data->toString());
    }

    #[Test]
    public function test_it_creates_transaction_instruction_with_program_id_and_keys_without_data()
    {
        $programId = new PublicKey('3Wnd5Df69KitZfUoPYZU438eFRNwGHkhLnSAWL65PxJX');
        $pk2 = new PublicKey('3Js7k6xYQbvXv6qUYLapYV7Sptfg37Tss9GcAyVEuUqk');
        $keys = [
            new AccountMeta($programId, true, true),
            new AccountMeta($pk2, false, true),
        ];
        $emptyData = Buffer::from([]);

        $instruction = new TransactionInstruction($programId, $keys);

        $this->assertEquals($programId, $instruction->programId);
        $this->assertEquals($keys, $instruction->keys);
        $this->assertEquals($emptyData, $instruction->data);
    }
}