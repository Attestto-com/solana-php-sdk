<?php

namespace Attestto\temp\SNS\Nft;

use Attestto\SolanaPhpSdk\Programs\SNS\Buffer;
use Attestto\SolanaPhpSdk\Programs\SNS\Exception;
use Attestto\SolanaPhpSdk\PublicKey;
use function Attestto\SolanaPhpSdk\Programs\SNS\deserialize;
use const Attestto\SolanaPhpSdk\Programs\SNS\MINT_PREFIX;
use const Attestto\SolanaPhpSdk\Programs\SNS\NAME_TOKENIZER_ID;


class NftRecord
{


    public function __construct($obj)
    {

    }

    public static function deserialize($data)
    {
        return new NftRecord(deserialize(self::$schema, $data));
    }

    public static function retrieve($connection, $key)
    {
        $accountInfo = $connection->getAccountInfo($key);
        if (!$accountInfo || !$accountInfo->data) {
            throw new Exception("NFT record not found");
        }
        return self::deserialize($accountInfo->data);
    }

    public static function findKey($nameAccount, $programId)
    {
        return PublicKey::findProgramAddress(
            [Buffer::from("nft_record"), $nameAccount->toBuffer()],
            $programId
        );
    }


    function getRecordFromMint($connection, $mint)
    {
        $filters = [
            [
                'memcmp' => [
                    'offset' => 0,
                    'bytes' => '3',
                ],
            ],
            [
                'memcmp' => [
                    'offset' => 1 + 1 + 32 + 32,
                    'bytes' => $mint->toBase58(),
                ],
            ],
        ];

        $result = $connection->getProgramAccounts(NAME_TOKENIZER_ID, [
            'filters' => $filters,
        ]);

        return $result;
    }

    const NAME_TOKENIZER_ID = new PublicKey(
        "nftD3vbNkNqfj2Sd3HZwbpw4BxxKWr4AjGb9X38JeZk"
    );

    const MINT_PREFIX = "tokenized_name";

    function getDomainMint($domain)
    {
        $mint = PublicKey::findProgramAddressSync(
            [MINT_PREFIX, $domain->toBuffer()],
            NAME_TOKENIZER_ID
        )[0];
        return $mint;
    }

//enum Tag {
//    Uninitialized = 0,
//    CentralState = 1,
//    ActiveRecord = 2,
//    InactiveRecord = 3,
//}
}



