<?php

namespace Attestto\SolanaPhpSdk\Accounts;

use Attestto\SolanaPhpSdk\Borsh\Borsh;
use Attestto\SolanaPhpSdk\Borsh\BorshDeserializable;
use Attestto\SolanaPhpSdk\Borsh\BorshObject;

class Metadata
{
    use BorshObject;

    public const SCHEMA = [
        Creator::class => Creator::SCHEMA[Creator::class],
        MetadataData::class => MetadataData::SCHEMA[MetadataData::class],
        self::class => [
            'kind' => 'struct',
            'fields' => [
                ['key', 'u8'],
                ['updateAuthority', 'pubkeyAsString'],
                ['mint', 'pubkeyAsString'],
                ['data', MetadataData::class],
                ['primarySaleHappened', 'u8'], // bool
                ['isMutable', 'u8'], // bool
            ],
        ],
    ];

    public static function fromBuffer(array $buffer): self
    {
        return Borsh::deserialize(self::SCHEMA, self::class, $buffer);
    }
}
