<?php

namespace Attestto\SolanaPhpSdk\Accounts;

use Attestto\SolanaPhpSdk\Accounts\Did\VerificationMethodStruct;
use Attestto\SolanaPhpSdk\Accounts\Did\ServiceStruct;
use Attestto\SolanaPhpSdk\Borsh\Borsh;
use Attestto\SolanaPhpSdk\Borsh\BorshDeserializable;
use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Exceptions\SNSError;
use Attestto\SolanaPhpSdk\Util\Buffer;


class NtfRecordAccount
{

    use BorshDeserializable;


    public const SCHEMA = [
        self::class => [
            'kind' => 'struct',
            'fields' => [
                ['tag', ['u8']],
                ['nonce', ['u8']],
                ['nameAccount', ['u8']], // len 32
                ['owner', ['u8']], // len 32
                ['nftMint', ['u8']], // len 32
            ],
        ],
    ];

    public static function deserialize(array $buffer): self
    {
        return Borsh::deserialize(self::SCHEMA, self::class, $buffer);
    }

    public static function retrieve(Connection $connection, PublicKey $key): self
    {
        $accountInfo = $connection->getAccountInfo($key);
        if (!$accountInfo || !$accountInfo['data']) {
            throw new \Exception("NFT record not found");
        }
        $base64String = base64_decode($accountInfo['data']);
        $uint8Array = array_values(unpack('C*', $base64String));
        return self::deserialize($uint8Array);
    }

    public static function findKey(PublicKey $nameAccount, PublicKey $programId)
    {
        return PublicKey::findProgramAddress(
            [Buffer::from("nft_record"), $nameAccount->toBuffer()],
            $programId
        );
    }

}

