<?php

namespace Attestto\SolanaPhpSdk\Programs\SNS;

use Attestto\SolanaPhpSdk\Connection;

use Attestto\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Attestto\SolanaPhpSdk\Exceptions\SNSError;
use Attestto\SolanaPhpSdk\Programs\SNS\State\NameRegistryStateAccount;
use Attestto\SolanaPhpSdk\PublicKey;


trait Bindings
{
    use Utils;
    /**
     * @throws SNSError
     * @throws AccountNotFoundException
     */
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
}
