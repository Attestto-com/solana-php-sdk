<?php

namespace Attestto\config\SNS;

use Attestto\SolanaPhpSdk\Buffer;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\SystemProgram;
use Attestto\SolanaPhpSdk\TokenProgramId;
use Attestto\SolanaPhpSdk\TransactionInstruction;

class CreateV2Instruction {
    public $tag;
    public $name;
    public $space;

    public static $schema = [
        'struct' => [
            'tag' => 'u8',
            'name' => 'string',
            'space' => 'u32',
        ],
    ];

    public function __construct(array $obj) {
        $this->tag = 9;
        $this->name = $obj['name'];
        $this->space = $obj['space'];
    }

    public function serialize(): Buffer {
        // Implement serialization logic
    }

    public function getInstruction(
        PublicKey $programId,
        PublicKey $rentSysvarAccount,
        PublicKey $nameProgramId,
        PublicKey $rootDomain,
        PublicKey $nameAccount,
        PublicKey $reverseLookupAccount,
        PublicKey $centralState,
        PublicKey $buyer,
        PublicKey $buyerTokenAccount,
        PublicKey $usdcVault,
        PublicKey $state
    ): TransactionInstruction {
        $data = $this->serialize();
        $keys = [
            [
                'pubkey' => $rentSysvarAccount,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $nameProgramId,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $rootDomain,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $nameAccount,
                'isSigner' => false,
                'isWritable' => true,
            ],
            [
                'pubkey' => $reverseLookupAccount,
                'isSigner' => false,
                'isWritable' => true,
            ],
            [
                'pubkey' => SystemProgram::programId(),
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $centralState,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $buyer,
                'isSigner' => true,
                'isWritable' => true,
            ],
            [
                'pubkey' => $buyerTokenAccount,
                'isSigner' => false,
                'isWritable' => true,
            ],
            [
                'pubkey' => $usdcVault,
                'isSigner' => false,
                'isWritable' => true,
            ],
            [
                'pubkey' => TokenProgramId::programId(),
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $state,
                'isSigner' => false,
                'isWritable' => false,
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
