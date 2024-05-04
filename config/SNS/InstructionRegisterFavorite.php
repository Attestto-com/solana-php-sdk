<?php

namespace Attestto\config\SNS;

use Attestto\SolanaPhpSdk\Borsh\Borsh;
use Attestto\SolanaPhpSdk\Buffer;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\TransactionInstruction;

class RegisterFavoriteInstruction
{
    public $tag;

    public const SCHEMA = [
        'struct' => [
            'tag' => 'u8',
        ],
    ];

    public function __construct()
    {
        $this->tag = 6;
    }

    public function serialize(): Buffer
    {
        return Borsh::serialize(self::SCHEMA, $this);
    }

    public function getInstruction(
        PublicKey $programId,
        PublicKey $nameAccount,
        PublicKey $favouriteAccount,
        PublicKey $owner,
        PublicKey $systemProgram
    ): TransactionInstruction
    {
        $data = $this->serialize();
        $keys = [
            [
                'pubkey' => $nameAccount,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $favouriteAccount,
                'isSigner' => false,
                'isWritable' => true,
            ],
            [
                'pubkey' => $owner,
                'isSigner' => true,
                'isWritable' => true,
            ],
            [
                'pubkey' => $systemProgram,
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
