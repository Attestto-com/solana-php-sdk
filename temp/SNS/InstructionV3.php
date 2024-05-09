<?php

namespace Attestto\temp\SNS;

use Attestto\SolanaPhpSdk\Borsh\Borsh;
use Attestto\SolanaPhpSdk\Buffer;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\TransactionInstruction;

class CreateInstructionV3
{
    public $tag;
    public $name;
    public $space;
    public $referrerIdxOpt;

    public const SCHEMA = [
        'struct' => [
            'tag' => 'u8',
            'name' => 'string',
            'space' => 'u32',
            'referrerIdxOpt' => ['option' => 'u16'],
        ],
    ];

    public function __construct(array $obj)
    {
        $this->tag = 13;
        $this->name = $obj['name'];
        $this->space = $obj['space'];
        $this->referrerIdxOpt = $obj['referrerIdxOpt'];
    }

    public function serialize(): Buffer
    {
        return Borsh::serialize(self::SCHEMA, $this);
    }

    public function getInstruction(
        PublicKey  $programId,
        PublicKey  $namingServiceProgram,
        PublicKey  $rootDomain,
        PublicKey  $name,
        PublicKey  $reverseLookup,
        PublicKey  $systemProgram,
        PublicKey  $centralState,
        PublicKey  $buyer,
        PublicKey  $buyerTokenSource,
        PublicKey  $pythMappingAcc,
        PublicKey  $pythProductAcc,
        PublicKey  $pythPriceAcc,
        PublicKey  $vault,
        PublicKey  $splTokenProgram,
        PublicKey  $rentSysvar,
        PublicKey  $state,
        ?PublicKey $referrerAccountOpt = null
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
                'pubkey' => $buyerTokenSource,
                'isSigner' => false,
                'isWritable' => true,
            ],
            [
                'pubkey' => $pythMappingAcc,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $pythProductAcc,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $pythPriceAcc,
                'isSigner' => false,
                'isWritable' => false,
            ],
            [
                'pubkey' => $vault,
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
        ];

        if (!is_null($referrerAccountOpt)) {
            $keys[] = [
                'pubkey' => $referrerAccountOpt,
                'isSigner' => false,
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
