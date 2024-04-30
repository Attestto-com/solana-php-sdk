<?php

namespace Attestto\SolanaPhpSdk\Programs\SNS;

use Attestto\SolanaPhpSdk\Exceptions\InputValidationException;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\Exceptions\GenericException;
use Attestto\SolanaPhpSdk\Accounts\NameRegistryStateAccount;
use Attestto\SolanaPhpSdk\Exceptions\SNSError;
use Attestto\SolanaPhpSdk\Util\Buffer;

class Utils
{

    // config.json file should be in the same directory as this file
    public mixed $config;
    public $centralStateSNSRecords;
    

    // Constructor
    public function __construct($config = null)
    {
        if ($config) {
            $this->config = $config;
        } else {
            $this->config = $this->loadConstants();
        }
        $sns_records_id = new PublicKey($this->config['BONFIDA_SNS_RECORDS_ID']);

        $this->centralStateSNSRecords = PublicKey::findProgramAddressSync(
            [$sns_records_id],
             $sns_records_id);

        return $this;
    }

    private function loadConstants()
    {
        $jsonFilePath = dirname(__DIR__) . '/SNS/Constants/config.json';
        return json_decode(file_get_contents($jsonFilePath), true);
    }

    public function getHashedNameSync(string $name): Buffer
    {
        $input = $this->config['HASH_PREFIX'] . $name;
        $hashed = hash('sha256', Buffer::from($input));
        return Buffer::from($hashed);
    }

    /**
     * @param Buffer $hashed_name
     * @param PublicKey|null $nameClass The name class public key
     * @param PublicKey|null $nameParent The name parent public key
     * @return PublicKey The public key of the name account
     * @throws InputValidationException
     * @deprecated Use {@link getNameAccountKeySync} instead
     */
    public function getNameAccountKeySync(
        Buffer $hashed_name,
        PublicKey $nameClass = null,
        PublicKey $nameParent = null
    ): PublicKey {
        $seeds = [$hashed_name];
        if ($nameClass) {
            $seeds[] = $nameClass->toBuffer();
        } else {
            $seeds[] = str_repeat("\0", 32);
        }
        if ($nameParent) {
            $seeds[] = $nameParent->toBuffer();
        } else {
            $seeds[] = str_repeat("\0", 32);
        }
        [$nameAccountKey] = PublicKey::findProgramAddressSync(
            $seeds,
            new PublicKey($this->config['NAME_PROGRAM_ID'])
        );
        return $nameAccountKey;
    }


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
     * @param string $domain The domain to compute the public key for (e.g `bonfida.sol`, `dex.bonfida.sol`)
     * @param string|null $record Optional parameter: If the domain being resolved is a record
     * @return array
     */
    function getDomainKeySync(string $domain, ?string $record = null): array {
        if (substr($domain, -4) === ".sol") {
            $domain = substr($domain, 0, -4);
        }
        $recordClass = $record === 'V2' ? $this->centralStateSNSRecords : null;
        $splitted = explode(".", $domain);
        if (count($splitted) === 2) {
            $prefix = $record ? $record : "\x00";
            $sub = $prefix . $splitted[0];
            $parentKey = $this->_deriveSync($splitted[1])['pubkey'];
            $result = $this->_deriveSync($sub, new PublicKey($parentKey), new PublicKey($recordClass));
            return array_merge($result, ['isSub' => true, 'parent' => $parentKey]);
        } else if (count($splitted) === 3 && $record) {
            // Parent key
            $parentKey = $this->_deriveSync($splitted[2])['pubkey'];
            // Sub domain
            $subKey = $this->_deriveSync("\0" . $splitted[1], new PublicKey($parentKey))['pubkey'];
            // Sub record
            $recordPrefix = $record === 'V2' ? "\x02" : "\x01";
            $result = $this->_deriveSync($recordPrefix . $splitted[0], new PublicKey($subKey), new PublicKey($recordClass));
            return array_merge($result, ['isSub' => true, 'parent' => $parentKey, 'isSubRecord' => true]);
        } else if (count($splitted) >= 3) {
            throw new SNSError(SNSError::InvalidInput);
        }
        $result = $this->_deriveSync($domain, new PublicKey($this->config['ROOT_DOMAIN_ACCOUNT']));
        return array_merge($result, ['isSub' => false, 'parent' => null]);
    }

    function _deriveSync(string $name, PublicKey $parent = null, PublicKey $classKey = null): array
    {
        // Assuming these functions exist elsewhere in your codebase
        $hashedDomainName = $this->getHashedNameSync($name);
        $pubkey = $this->getNameAccountKeySync($hashedDomainName, $classKey, $parent ?: new PublicKey($this->config['ROOT_DOMAIN_ACCOUNT']));
        return ['pubkey' => $pubkey, 'hashed' => $hashed];
    }

}
