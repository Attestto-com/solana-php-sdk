<?php

namespace Attestto\SolanaPhpSdk\Programs\SplToken\State;

use Attestto\SolanaPhpSdk\Borsh\Borsh;
use Attestto\SolanaPhpSdk\Borsh\BorshObject;
use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Util\Commitment;

/**
 * @property mixed|null $mint
 */
class Account
{

    use BorshObject;
    protected const TOKEN_PROGRAM_ID = 'TokenkegQfeZyiNwAJbNbGKPFXCWuBvf9Ss623VQ5DA';
    private static PublicKey $address;
    private static mixed $tlvData;



    public const SCHEMA = [
        self::class => [
            'kind' => 'struct',
            'fields' => [
                ['mint', 'pubKey'],
                ['owner', 'pubKey'],
                ['amount', 'u64'],
                ['delegateOption', 'u32'],
                ['delegate', 'pubKey'],
                ['state', 'u8'],
                ['isNativeOption', 'u8'],
                ['isNative', 'u8'],
                ['delegatedAmount', 'u64'],
                ['closeAuthorityOption', 'u32'],
                ['closeAuthority', 'pubKey']
            ],
        ],
    ];

    public static function fromBuffer(array $buffer): self
    {
        return Borsh::deserialize(self::SCHEMA, self::class, $buffer);
    }

    /**
     * @throws AccountNotFoundException
     */
    public static function getAccount(
        Connection $connection,
        PublicKey $accountPublicKeyOnbject,
        Commitment $commitment = null,
        $programId = new PublicKey(self::TOKEN_PROGRAM_ID)
    ): Account
    {
        try {
            $info = $connection->getAccountInfo($accountPublicKeyOnbject, $commitment);
            self::$address = $accountPublicKeyOnbject;
            self::$tlvData = $info['data'];
            $base64Data = $info['data']['0'];
            $base64String = base64_decode($base64Data);
            $uint8Array = array_values(unpack('C*', $base64String));
            return self::fromBuffer($uint8Array);
        } catch (AccountNotFoundException $e) {
            throw new AccountNotFoundException();
        }
    }
}
