<?php

namespace Attestto\temp\SNS;

use Attestto\SolanaPhpSdk\Borsh\Borsh;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\Util\Buffer;


class InstructionBurn {
    public $tag;

    public const SCHEMA = [

        self::class => [
            'kind' => 'struct',
            'fields' => [
                ['tag', 'u8']
            ],
        ],
    ];

    public function __construct() {
        $this->tag = 16;
    }

    public function serialize(): Buffer {
        $array = Borsh::serialize(self::SCHEMA, $this);
        return Buffer::from($array);
    }

    public function getInstruction(
        PublicKey $programId,
        PublicKey $nameServiceId,
        PublicKey $systemProgram,
        PublicKey $domain,
        PublicKey $reverse,
        PublicKey $resellingState,
        PublicKey $state,
        PublicKey $centralState,
        PublicKey $owner,
        PublicKey $target
    ): TransactionInstruction {
        $data = $this->serialize();
        $keys = [
            [
                'pubkey' => $nameServiceId,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $systemProgram,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $domain,
                'isSigner' => false,
                'isWritable' => true,
            ],
            [
                'pubkey' => $reverse,
                'isSigner' => false,
                'isWritable' => true,
            ],
            [
                'pubkey' => $resellingState,
                'isSigner' => false,
                'isWritable' => true,
            ],
            [
                'pubkey' => $state,
                'isSigner' => false,
                'isWritable' => true,
            ],
            [
                'pubkey' => $centralState,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $owner,
                'isSigner' => true,
                'isWritable' => false,
            ],
            [
                'pubkey' => $target,
                'isSigner' => false,
                'isWritable' => true,
            ],
        ];

        return new TransactionInstruction([
            'keys' => $keys,
            'programId' => $programId,
            'data' => $data,
        ]);
    }
}

?>
