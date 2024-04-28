<?php

namespace Attestto\SolanaPhpSdk\Programs;

use Attestto\SolanaPhpSdk\Buffer;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\Borsh\Borsh;

class CreateReverseInstruction {
    public $tag;
    public $name;

    public const SCHEMA = [
        'struct' => [
            'tag' => 'u8',
            'name' => 'string',
        ],
    ];

    public function __construct(array $obj) {
        $this->tag = 12;
        $this->name = $obj['name'];
    }

    public function serialize(): Buffer {
        return Borsh::serialize(self::SCHEMA, $this);
    }

    public function getInstruction(
        PublicKey $programId,
        PublicKey $namingServiceProgram,
        PublicKey $rootDomain,
        PublicKey $reverseLookup,
        PublicKey $systemProgram,
        PublicKey $centralState,
        PublicKey $feePayer,
        PublicKey $rentSysvar,
        ?PublicKey $parentName = null,
        ?PublicKey $parentNameOwner = null
    ): TransactionInstruction {
        $data = $this->serialize();
        $keys = [
            [
                'pubkey' => $namingServiceProgram,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $rootDomain,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $reverseLookup,
                'isSigner' => false,
                'isWritable' => true,
            ],
            [
                'pubkey' => $systemProgram,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $centralState,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $feePayer,
                'isSigner' => true,
                'isWritable' => true,
            ],
            [
                'pubkey' => $rentSysvar,
                'isSigner' => false,
                'isWritable' => false,
            ],
        ];

        if (!is_null($parentName)) {
            $keys[] = [
                'pubkey' => $parentName,
                'isSigner' => false,
                'isWritable' => true,
            ];
        }

        if (!is_null($parentNameOwner)) {
            $keys[] = [
                'pubkey' => $parentNameOwner,
                'isSigner' => true,
                'isWritable' => true,
            ];
        }

        return new TransactionInstruction([
            'keys' => $keys,
            'programId' => $programId,
            'data' => $data,
        ]);
    }
}

?>
