<?php

namespace Attestto\SolanaPhpSdk\Programs;

use Attestto\SolanaPhpSdk\Program;
use Attestto\SolanaPhpSdk\Accounts\Sns\NtfRecordAccount;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Connection;
use StephenHill\Base58;
use Attestto\SolanaPhpSdk\SolanaRpcClient;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\Util\AccountMeta;
use Attestto\SolanaPhpSdk\Util\Buffer;

/**
 * Class DidSolProgram
 * 
 * This class represents a program for interacting with the Solana blockchain using the DID (Decentralized Identifier) protocol.
 * It provides methods for creating and managing DID accounts, signing and verifying messages, and other related operations.
 * @version 1.0
 * @package Attestto\SolanaPhpSdk\
 * @license MIT
 * @author Eduardo Chongkan
 * @link https://chongkan.com
 * @see https://github.com/identity-com/sol-did
 */

class SplSnsProgram extends Program
{
    public const NAME_TOKENIZER_ID = 'nftD3vbNkNqfj2Sd3HZwbpw4BxxKWr4AjGb9X38JeZk';
    public const MINT_PREFIX = 'tokenized_name';

    // public function createSubDomain($subdomain) : TransactionInstruction {
    //     $ix = new TransactionInstruction(
    //         new PublicKey(self::DIDSOL_PROGRAM_ID),
    //         [
    //             new AccountMeta(new PublicKey($this->publicKey), true, true),
    //             new AccountMeta(new PublicKey($this->publicKey), false, true),
    //         ],
    //         $subdomain
    //     );
    // }

    // public static function retrieve(Connection $connection, PublicKey $key, $accountType): self
    // {
    //     $accountInfo = $connection->getAccountInfo($key);
    //     if (!$accountInfo || !$accountInfo['data']) {
    //         throw new \Exception("NFT record not found");
    //     }
    //     $base64String = base64_decode($accountInfo['data']);
    //     $uint8Array = array_values(unpack('C*', $base64String));
    //     return self::deserialize($uint8Array);
    // }

    /**
     * This function can be used to retrieve a NFT Record given a mint
     *
     * @param connection A solana RPC connection
     * @param mint The mint of the NFT Record
     * @returns
     */
    public function getRecordFromMint(string $pubKey)
    {
        $magicOffsetNumber = 0; 

        return $this->client->call('getProgramAccounts', [
            self::NAME_TOKENIZER_ID,
            [
                'encoding' => 'base64',
                'filters' => [
                    [
                        'memcmp' => [
                            'bytes' => '3',
                            'offset' => $magicOffsetNumber,
                        ],
                    ],
                    [
                        'memcmp' => [
                            'bytes' => $pubKey,
                            'offset' => 1 + 1 + 32 + 32,
                        ],
                    ],
                ],
            ],
        ]);
    }



}
