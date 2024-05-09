<?php

namespace Attestto\temp\SNS;

use Attestto\SolanaPhpSdk\Borsh\Borsh;
use Attestto\SolanaPhpSdk\Buffer;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\TransactionInstruction;

class CreateWithNftInstruction
{
    public $tag;
    public $name;
    public $space;

    public const SCHEMA = [
        'struct' => [
            'tag' => 'u8',
            'name' => 'string',
            'space' => 'u32',
        ],
    ];

    public function __construct(array $obj)
    {
        $this->tag = 17;
        $this->name = $obj['name'];
        $this->space = $obj['space'];
    }

    public function serialize(): Buffer
    {
        return Borsh::serialize(self::SCHEMA, $this);
    }

    public function getInstruction(
        PublicKey $programId,
        PublicKey $namingServiceProgram,
        PublicKey $rootDomain,
        PublicKey $name,
        PublicKey $reverseLookup,
        PublicKey $systemProgram,
        PublicKey $centralState,
        PublicKey $buyer,
        PublicKey $nftSource,
        PublicKey $nftMetadata,
        PublicKey $nftMint,
        PublicKey $masterEdition,
        PublicKey $collection,
        PublicKey $splTokenProgram,
        PublicKey $rentSysvar,
        PublicKey $state,
        PublicKey $mplTokenMetadata
    ): TransactionInstruction
    {
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
                'pubkey' => $name,
                'isSigner' => false,
                'isWritable' => true,
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
                'pubkey' => $buyer,
                'isSigner' => true,
                'isWritable' => true,
            ],
            [
                'pubkey' => $nftSource,
                'isSigner' => false,
                'isWritable' => true,
            ],
            [
                'pubkey' => $nftMetadata,
                'isSigner' => false,
                'isWritable' => true,
            ],
            [
                'pubkey' => $nftMint,
                'isSigner' => false,
                'isWritable' => true,
            ],
            [
                'pubkey' => $masterEdition,
                'isSigner' => false,
                'isWritable' => true,
            ],
            [
                'pubkey' => $collection,
                'isSigner' => false,
                'isWritable' => true,
            ],
            [
                'pubkey' => $splTokenProgram,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $rentSysvar,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $state,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $mplTokenMetadata,
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
