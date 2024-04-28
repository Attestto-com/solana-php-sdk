<?php

namespace Attestto\SolanaPhpSdk\Accounts;

use Attestto\SolanaPhpSdk\Accounts\Did\VerificationMethodStruct;
use Attestto\SolanaPhpSdk\Accounts\Did\ServiceStruct;
use Attestto\SolanaPhpSdk\Borsh\Borsh;
use Attestto\SolanaPhpSdk\Borsh\BorshDeserializable;
use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Exceptions\SNSError;


/**
 * Class DidData
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

class NameRegistryStateAccount
{

    use BorshDeserializable;


    public const SCHEMA = [
        self::class => [
            'kind' => 'struct',
            'fields' => [
                ['parentName', ['u8']],
                ['owner', ['u8']],
                ['class', ['u8']]
            ],
        ],
    ];

    public static function retrieve(Connection $connection, PublicKey $nameAccountKey)
    {
        $nameAccount = $connection->getAccountInfo($nameAccountKey);
        if (!$nameAccount) {
            throw new SNSError(SNSError::AccountDoesNotExist);
        }

        $res = new NameRegistryStateAccount(
            self::deserialize($nameAccount['data'])
        );
        //$res->data = $nameAccount->data->slice($this->config->SOL_RECORD_SIG_LEN);

        $nftOwner = retrieveNftOwner($connection, $nameAccountKey);

        return ['registry' => $res, 'nftOwner' => $nftOwner];
    }

    public static function deserialize(array $buffer): self
    {
        return Borsh::deserialize(self::SCHEMA, self::class, $buffer);
    }
}



