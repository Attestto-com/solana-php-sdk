<?php

namespace Attestto\SolanaPhpSdk\Programs;

use Attestto\SolanaPhpSdk\Exceptions\BaseSolanaPhpSdkException;
use Attestto\SolanaPhpSdk\Program;
use Attestto\SolanaPhpSdk\Accounts\DidData;
use Attestto\SolanaPhpSdk\PublicKey;
use StephenHill\Base58;
use Attestto\SolanaPhpSdk\SolanaRpcClient;

/**
 * Class DidSolProgram - Work In Progress
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

class DidSolProgram extends Program
{
    public const DIDSOL_PROGRAM_ID = 'didso1Dpqpm4CsiCjzP766BGY89CAdD6ZBL68cRhFPc';
    public const DIDSOL_DEFAULT_SEED = 'did-account';

    /**
     * getDidDataAcccountInfo
     *
     * @param SolanaRpcClient|string $client The RPC client or the custom RPC endpoint URL to use.
     * @param string $base58SubjectPk The Public Key of the DID.
     * @return string (JSON) The account info of the DID data account as it comes from the RPC
     * @example DidSolProgram::getDidDataAcccountInfo($client, 'did:sol:3Js7k6xYQbvXv6qUYLapYV7Sptfg37Tss9GcAyVEuUqk', false);
     */
    static function getDidDataAcccountInfo($client, $base58SubjectPk)
    {
        $pdaPublicKey =  self::getDidDataAccountId($base58SubjectPk);
        return $client->call('getAccountInfo', [$pdaPublicKey, ["encoding" => "jsonParsed"]])['value'];
        // Data is always returned in base54 because it exceeds 128 bytes
    }

    /**
     * getDidDataAccountId
     *
     * @param string $did 'did:sol:[cluster]....'
     * @return string The base58 encoded public key of the DID data account
     * @throws BaseSolanaPhpSdkException
     * @example DidSolProgram::getDidDataAccountId('did:sol:devnet:3Js7k6xYQbvXv6qUYLapYV7Sptfg37Tss9GcAyVEuUqk');
     */
    static function getDidDataAccountId($base58SubjectPk): string
    {

        $b58 = new Base58();
        $seeds = array(self::DIDSOL_DEFAULT_SEED, $b58->decode($base58SubjectPk));
        $pId = new PublicKey(self::DIDSOL_PROGRAM_ID);
        $publicKey =  PublicKey::findProgramAddress($seeds, $pId);

        return $publicKey[0]->toBase58();
    }

    /**
     * deserializeDidData
     *
     * @param string $dataBase64 The base64 encoded data of the DID data account
     * @return DidData The deserialized DID data object
     * @example DidSolProgram::deserializeDidData('TVjvjfsd7fMA/gAAAA...');
     */
    static function deserializeDidData($dataBase64)
    {

        $base64String = base64_decode($dataBase64);
        $uint8Array = array_values(unpack('C*', $base64String));
        $didData = DidData::fromBuffer($uint8Array);

        $keyData = $didData->keyData;

        $binaryString = pack('C*', ...$keyData);

        $b58 = new Base58();
        $base58String = $b58->encode($binaryString);
        $didData->keyData = $base58String;
        return $didData;
    }


    public function parse(string $did) : array
    {

        $did = explode(":", $did);
        if ($did[0] !== 'did' ||  count($did) < 3) {
            throw new \Exception('Invalid DID format, use did:sol:[network:]base58SubjectPK');
        }
        if ($did[1] !== 'sol') {
            throw new \Exception('Unsupported DID method, use did:sol:[network:]base58SubjectPK');
        }
        if (count($did) == 4) {
            $network = $did[2];
            $base58SubjectPK = $did[3];
        }else if (count($did) == 3) {
            $network = 'mainnet';
            $base58SubjectPK = $did[2];
        }

        $rpcEndpoint = $this->getRpcEndpointFromShortcut($network);

        return [
            'network' => $network,
            'base58SubjectPK' => $base58SubjectPK,
            'dataAccountId' => self::getDidDataAccountId($base58SubjectPK),
            'rpcEndpoint' => $rpcEndpoint
        ];
    }

    private function getRpcEndpointFromShortcut(string $network){
        switch ($network) {
            case 'mainnet':
                $rpcEndpoint = SolanaRpcClient::MAINNET_ENDPOINT;
                break;
            case 'devnet':
                $rpcEndpoint = SolanaRpcClient::DEVNET_ENDPOINT;
                break;
            case 'testnet':
                $rpcEndpoint = SolanaRpcClient::TESTNET_ENDPOINT;
                break;
            default:
                $rpcEndpoint = SolanaRpcClient::MAINNET_ENDPOINT;
        }
        return $rpcEndpoint;
    }


}
