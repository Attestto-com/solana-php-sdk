<?php

namespace Attestto\SolanaPhpSdk\Programs\SNS;

use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\Exceptions\GenericException;
use Attestto\SolanaPhpSdk\Accounts\NameRegistryStateAccount;
use Attestto\SolanaPhpSdk\Exceptions\SNSError;


class Utils
{

    // config.json file should be in the same directory as this file
    public $config;

    // Constructor
    public function __construct($config = null)
    {
        if ($config) {
            $this->config = $config;
        } else {
            $this->config = $this->loadConstants();
        }
        return $this;
    }

    private function loadConstants()
    {
        $jsonFilePath = dirname(__DIR__) . '/SNS/Constants/config.json';
        return json_decode(file_get_contents($jsonFilePath), true);
    }

    public function getHashedNameSync(string $name): string
    {
        $input = $this->config['HASH_PREFIX'] . $name;
        $hashed = hash('sha256', $input);
        return $hashed;
    }

    /**
     * @deprecated Use {@link getNameAccountKeySync} instead
     * @param string $hashedName The hashed name buffer
     * @param PublicKey|null $nameClass The name class public key
     * @param PublicKey|null $nameParent The name parent public key
     * @return PublicKey The public key of the name account
     */
    function getNameAccountKey(string $hashedName, ?PublicKey $nameClass = null, ?PublicKey $nameParent = null): PublicKey
    {
        $seeds = [$hashedName];
        if ($nameClass) {
            $seeds[] = $nameClass->toBuffer();
        } else {
            $seeds[] = str_repeat("\0", 32); // Buffer.alloc(32)
        }
        if ($nameParent) {
            $seeds[] = $nameParent->toBuffer();
        } else {
            $seeds[] = str_repeat("\0", 32); // Buffer.alloc(32)
        }
        $result = PublicKey::findProgramAddress($seeds, NAME_PROGRAM_ID);
        return $result[0];
    }

    //-- 
    /**
     * This function can be used to perform a reverse look up
     * @param connection The Solana RPC connection
     * @param nameAccount The public key of the domain to look up
     * @returns The human readable domain name
     */
    public function reverseLookup(Connection $connection, PublicKey $nameAccount): string
    {
        $hashedReverseLookup = $this->getHashedNameSync($nameAccount->toBase58());
        $reverseLookupAccount = $this->getNameAccountKeySync($hashedReverseLookup, $this->config->REVERSE_LOOKUP_CLASS);

        $registry = NameRegistryStateAccount::retrieve($connection, $reverseLookupAccount);
        if (!$registry['data']) {
            throw new SNSError(SNSError::NoAccountData);
        }

        return $this->deserializeReverse($registry['data']);
    }

    public function deserializeReverse(
        $data
    ): ?string {
        if (!$data) {
            return null;
        }
        $nameLength = unpack('V', substr($data, 0, 4))[1];
        return substr($data, 4, $nameLength);
    }


    /**
    * This function can be used to compute the public key of a domain or subdomain
    * @deprecated Use {@link getDomainKeySync} instead
    * @param string $domain The domain to compute the public key for (e.g `bonfida.sol`, `dex.bonfida.sol`)
    * @param bool $record Optional parameter: If the domain being resolved is a record
    * @return array
    * @throws SNSError
    */
    function getDomainKey(string $domain, bool $record = false): array
    {
        if (substr($domain, -4) === '.sol') {
            $domain = substr($domain, 0, -4);
        }
        $splitted = explode('.', $domain);
        if (count($splitted) === 2) {
            $prefix = $record ? "\x01" : "\x00";
            $sub = $prefix . $splitted[0];
            $parentKey = $this->_derive($splitted[1])['pubkey'];
            $result = $this->_derive($sub, $parentKey);
            return array_merge($result, ['isSub' => true, 'parent' => $parentKey]);
        } elseif (count($splitted) === 3 && $record) {
            // Parent key
            $parentKey = $this->_derive($splitted[2])['pubkey'];
            // Sub domain
            $subKey = $this->_derive("\x00" . $splitted[1], $parentKey)['pubkey'];
            // Sub record
            $recordPrefix = "\x01";
            $result = $this->_derive($recordPrefix . $splitted[0], $subKey);
            return array_merge($result, ['isSub' => true, 'parent' => $parentKey, 'isSubRecord' => true]);
        } elseif (count($splitted) >= 3) {
            throw new SNSError(ErrorType::InvalidInput);
        }
        $result = $this->_derive($domain, ROOT_DOMAIN_ACCOUNT);
        return array_merge($result, ['isSub' => false, 'parent' => null]);
    }

    private function _derive(
        string $name,
        PublicKey $parent = ROOT_DOMAIN_ACCOUNT
    ): array {
        $hashed = $this->getHashedNameSync($name);
        $pubkey = $this->getNameAccountKeySync($hashed, null, $parent);
        return ['pubkey' => $pubkey, 'hashed' => $hashed];
    }

    /**
    * This function can be used to get the key of the reverse account
    * @deprecated Use {@link getReverseKeySync} instead
    * @param string $domain The domain to compute the reverse for
    * @param bool|null $isSub Whether the domain is a subdomain or not
    * @return PublicKey The public key of the reverse account
    */
    function getReverseKey(string $domain, ?bool $isSub = false): PublicKey
    {
        $domainKey = $this->getDomainKey($domain);
        $hashedReverseLookup = $this->getHashedName($domainKey['pubkey']->toBase58());
        $reverseLookupAccount = $this->getNameAccountKey($hashedReverseLookup, REVERSE_LOOKUP_CLASS, $isSub ? $domainKey['parent'] : null);
        return $reverseLookupAccount;
    }
}
