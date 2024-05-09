<?php

namespace Attestto\SolanaPhpSdk\Programs;

use Attestto\SolanaPhpSdk\Exceptions\BaseSolanaPhpSdkException;
use Attestto\SolanaPhpSdk\Program;
use Attestto\SolanaPhpSdk\Programs\SNS\Bindings;
use Attestto\SolanaPhpSdk\Programs\SNS\Utils;
use Attestto\SolanaPhpSdk\Programs\SNS\Instructions\Instructions;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\SolanaRpcClient;


class SnsProgram extends Program
{

    use Instructions;
    use Utils;
    use Bindings;

    public mixed $config;


    public function __construct(SolanaRpcClient $client, $config = null)
    {
        parent::__construct($client);
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


}
