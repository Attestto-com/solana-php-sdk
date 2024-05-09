<?php

namespace Attestto\SolanaPhpSdk\Programs\SNS;

use Attestto\SolanaPhpSdk\Connection;

use Attestto\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Attestto\SolanaPhpSdk\Exceptions\SNSError;
use Attestto\SolanaPhpSdk\Programs\SNS\State\NameRegistryStateAccount;
use Attestto\SolanaPhpSdk\Programs\SNS\State\ReverseInstructionAccount;
use Attestto\SolanaPhpSdk\Programs\SystemProgram;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\Util\Buffer;
use Exception;
use function PHPUnit\Framework\isInstanceOf;

/**
 * @method createInstruction($NAME_PROGRAM_ID, $programId, PublicKey $nameAccountKey, PublicKey $nameOwner, PublicKey $payerKey, Buffer $hashed_name, Buffer $param, Buffer $param1, PublicKey|null $nameClass, PublicKey|null $parentName, $nameParentOwner)
 * @method getReverseKeySync(string $subdomain, true $true)

 * @method getNameOwner(Connection $connection, PublicKey $parentName)
 * @method retrieve(Connection $connection, mixed $parent)
 * @method transferInstruction(mixed $NAME_PROGRAM_ID, mixed $pubkey, PublicKey $newOwner, PublicKey|null $owner, $null, mixed $nameParent, $nameParentOwner)
 */
trait Bindings
{
//    use Utils;
//    use Instructions;


    public function createSubdomain(
        Connection $connection,
        string $subdomain,
        PublicKey $owner,
        int $space = 2000,
        PublicKey $feePayer = null
    ): array
    {
        $ixs = [];
        $sub = explode(".", $subdomain)[0];
        if (!$sub) {
            throw new SNSError(SNSError::InvalidSubdomain);
        }

        $domainKeySync = $this->getDomainKeySync($subdomain);
        $parent = $domainKeySync['parent'];
        $pubkey = $domainKeySync['pubkey'];

        $lamports = $connection->getMinimumBalanceForRentExemption(
            $space + NameRegistryStateAccount::SOL_RECORD_SIG_LEN
        );

        $ix_create = $this->createNameRegistry(
            $connection,
            "\0" . $sub,
            $space,
            $feePayer ?? $owner,
            $owner,
            $lamports,
            null,
            $parent
        );
        $ixs[] = $ix_create;

        $reverseKey = $this->getReverseKeySync($subdomain, true);
        $info = $connection->getAccountInfo($reverseKey);
        if (!$info['data']) {
            $reverseName = $this->createReverseName(
                $pubkey,
                "\0" . $sub,
                $feePayer ?? $owner,
                $parent,
                $owner
            );
            $ixs = array_merge($ixs, $reverseName[1]);
        }

        return [[], $ixs];
    }
    /**
     * @throws SNSError
     * @throws AccountNotFoundException
     * @throws Exception
     */
    public function createSubdomainFast(
        Connection $connection,
        string $subdomain,
        PublicKey $subdomainPk,
        PublicKey $parentPk,
        PublicKey $owner,
        int $space = 1000,
        PublicKey $feePayer = null
    ): array
    {
        $ixs = [];
        $sub = explode(".", $subdomain)[0];
        if (!$sub) {
            throw new SNSError(SNSError::InvalidSubdomain);
        }

//        $domainKeySync = $this->getDomainKeySync($subdomain);
//        $parent = $domainKeySync['parent'];
//        $pubkey = $domainKeySync['pubkey'];

        $lamports = 0.01 * 10 ** 9; // 0.01 SOL

        $ix_create = $this->createNameRegistry(
            $connection,
            "\0" . $sub,
            $space + NameRegistryStateAccount::SOL_RECORD_SIG_LEN,
            $feePayer ?? $owner,
            $owner,
            $lamports,
            null,
            $parentPk
        );
        $ixs[] = $ix_create;

        //$reverseKey = $this->getReverseKeySync($subdomain, true);
       // $info = $connection->getAccountInfo($reverseKey);
        //if (!$info['data']) {
            $reverseName = $this->createReverseName(
                $subdomainPk,
                "\0" . $sub,
                $feePayer ?? $owner,
                $parentPk,
                $owner
            );
            $ixs = array_merge($ixs, $reverseName[1]);
       // }

        return [[], $ixs];
    }

    /**
     * Creates a name account with the given rent budget, allocated space, owner and class.
     *
     * @param Connection $connection The solana connection object to the RPC node
     * @param string $name The name of the new account
     * @param int $space The space in bytes allocated to the account
     * @param PublicKey $payerKey The allocation cost payer
     * @param PublicKey $nameOwner The pubkey to be set as owner of the new name account
     * @param int|null $lamports The budget to be set for the name account. If not specified, it'll be the minimum for rent exemption
     * @param PublicKey|null $nameClass The class of this new name
     * @param PublicKey|null $parentName The parent name of the new name. If specified its owner needs to sign
     * @return TransactionInstruction
     * @throws Exception
     */
    public function createNameRegistry(
        Connection $connection,
        string $name,
        int $space,
        PublicKey $payerKey,
        PublicKey $nameOwner,
        int $lamports = null,
        PublicKey $nameClass = null,
        PublicKey $parentName = null
    ): TransactionInstruction
    {
        $hashed_name = $this->getHashedNameSync($name);
        $nameAccountKey = $this->getNameAccountKeySync($hashed_name, $nameClass, $parentName);

        $balance = $lamports ?: $connection->getMinimumBalanceForRentExemption($space);

        $nameParentOwner = $parentName;
        if ($parentName) { // TODO review logic
            $parentAccount = $this->getNameOwner($connection, $parentName->toBase58());
            $nameParentOwner = $parentAccount['registry']->owner;
        }

        return $this->createInstruction(
            new PublicKey($this->config['NAME_PROGRAM_ID']),
            SystemProgram::programId(),
            $nameAccountKey,
            $nameOwner,
            $payerKey,
            $hashed_name,
            new Buffer($balance, Buffer::TYPE_LONG, false),
            new Buffer($space, Buffer::TYPE_INT, false),
            $nameClass,
            $parentName,
            $nameParentOwner
        );
    }



    /**
     * This function is used to transfer the ownership of a subdomain in the Solana Name Service.
     *
     * @param Connection $connection The Solana RPC connection object.
     * @param string $subdomain The subdomain to transfer. It can be with or without .sol suffix (e.g., 'something.bonfida.sol' or 'something.bonfida').
     * @param PublicKey $newOwner The public key of the new owner of the subdomain.
     * @param bool $isParentOwnerSigner A flag indicating whether the parent name owner is signing this transfer.
     * @param PublicKey|null $owner The public key of the current owner of the subdomain. This is an optional parameter. If not provided, the owner will be resolved automatically. This can be helpful to build transactions when the subdomain does not exist yet.
     *
     * @return TransactionInstruction
     * @throws Exception
     */
    public function transferSubdomain(
        Connection $connection,
        string $subdomain,
        PublicKey $newOwner,
        bool $isParentOwnerSigner = false,
        PublicKey $owner = null
    ): TransactionInstruction
    {
        $domainKeySync = $this->getDomainKeySync($subdomain);
        $pubkey = $domainKeySync['pubkey'];
        $isSub = $domainKeySync['isSub'];
        $parent = $domainKeySync['parent'];

        if (!$parent || !$isSub) {
            throw new SNSError(SNSError::InvalidSubdomain);
        }

        if (!$owner) {
            $registry = $this->retrieve($connection, $pubkey);
            $owner = $registry['registry']->owner;
        }

        $nameParent = null;
        $nameParentOwner = null;

        if ($isParentOwnerSigner) {
            $nameParent = $parent;
            $parentAccount = $this->retrieve($connection, $parent);
            $nameParentOwner = $parentAccount['registry']->owner;
        }

        return $this->transferInstruction(
            $this->config['NAME_PROGRAM_ID'],
            $pubkey,
            $newOwner,
            $owner,
            null,
            $nameParent,
            $nameParentOwner
        );
    }

    /**
     * This function is used to create a reverse name.
     *
     * @param PublicKey $nameAccount The name account to create the reverse account for
     * @param string $name The name of the domain
     * @param PublicKey $feePayer The fee payer of the transaction
     * @param PublicKey|null $parentName The parent name account
     * @param PublicKey|null $parentNameOwner The parent name owner
     * @return array
     * @throws Exception
     */
    public function createReverseName(
        PublicKey $nameAccount,
        string $name,
        PublicKey $feePayer,
        PublicKey $parentName = null,
        PublicKey $parentNameOwner = null
    ): array {
//        $centralState = $this->findProgramAddress(
//            [$this->config['REGISTER_PROGRAM_ID']->toBuffer()],
//            $this->config['REGISTER_PROGRAM_ID']
//        )[0];

        $hashedReverseLookup = $this->getHashedNameSync($nameAccount->toBase58());
        $reverseLookupAccount = $this->getNameAccountKeySync(
            $hashedReverseLookup,
            $this->centralStateSNSRecords,
            $parentName
        );

        $initCentralStateInstruction = new ReverseInstructionAccount($name);
        $initCentralStateInstruction->getInstruction(
            new PublicKey($this->config['REGISTER_PROGRAM_ID']),
            new PublicKey($this->config['NAME_PROGRAM_ID']),
            new PublicKey($this->config['ROOT_DOMAIN_ACCOUNT']),

            $reverseLookupAccount,
            SystemProgram::programId(),
            $this->centralStateSNSRecords,
            $feePayer,
            new PublicKey($this->config['SYSVAR_RENT_PUBKEY']),
            $parentName,
            $parentNameOwner
        );

        $instructions = [$initCentralStateInstruction];

        return [[], $instructions];
    }



}
