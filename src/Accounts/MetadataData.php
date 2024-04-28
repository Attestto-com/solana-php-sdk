<?php

namespace Attestto\SolanaPhpSdk\Accounts;

use Attestto\SolanaPhpSdk\Borsh;

class MetadataData
{
    use Borsh\BorshObject;

    public const SCHEMA = [
        Creator::class => Creator::SCHEMA[Creator::class],
        self::class => [
            'kind' => 'struct',
            'fields' => [
                ['name', 'string'],
                ['symbol', 'string'],
                ['uri', 'string'],
                ['sellerFeeBasisPoints', 'u16'],
                ['creators', [
                    'kind' => 'option',
                    'type' => [Creator::class]
                ]]
            ],
        ],
    ];

    public function __set($name, $value): void
    {
        $this->{$name} = is_string($value) ? preg_replace('/[[:cntrl:]]/', '', $value) : $value;
    }
}
