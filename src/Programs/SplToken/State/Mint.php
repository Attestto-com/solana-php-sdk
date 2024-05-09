<?php

namespace Attestto\SolanaPhpSdk\Programs\SplToken\State;

use Attestto\SolanaPhpSdk\Borsh\Borsh;
use Attestto\SolanaPhpSdk\Borsh\BorshObject;

/**
 * Class Mint
 *
 * This class represents a Decentralized Identifier (DID) account.
 * It provides methods for creating and managing DID accounts, signing and verifying messages, and other related operations.
 * @version 1.0
 * @package Attestto\SolanaPhpSdk\Accounts
 * @license MIT
 * @author Eduardo Chongkan
 * @link https://chongkan.com
 * @see https://github.com/identity-com/sol-did/tree/develop/sol-did/client/packages/idl
 * @see https://explorer.solana.com/address/didso1Dpqpm4CsiCjzP766BGY89CAdD6ZBL68cRhFPc/anchor-program?cluster=devnet
 */

class Mint
{

    use BorshObject;

    public const SCHEMA = [
        self::class => [
            'kind' => 'struct',
            'fields' => [
                ['mintAuthorityOption', 'u32'],
                ['mintAuthority', 'pubKey'],
                ['supply', 'u64'],
                ['decimals', 'u8'],
                ['isInitialized', 'u8'],
                ['freezeAuthorityOption', 'u32'],
                ['freezeAuthority', 'pubKey']
            ],
        ],
    ];

    public static function fromBuffer(array $buffer): self
    {
        return Borsh::deserialize(self::SCHEMA, self::class, $buffer);
    }
}
