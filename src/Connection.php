<?php

namespace Attestto\SolanaPhpSdk;

use Attestto\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Attestto\SolanaPhpSdk\Util\Commitment;

class Connection extends Program
{
    /**
     * @param string $pubKey
     * @return array
     */
    public function getAccountInfo(string $pubKey): array
    {
        $accountResponse = $this->client->call('getAccountInfo', [$pubKey, ["encoding" => "jsonParsed"]])['value'];

        if (! $accountResponse) {
            throw new AccountNotFoundException("API Error: Account {$pubKey} not found.");
        }

        return $accountResponse;
    }

    /**
     * @param string $pubKey
     * @return float
     */
    public function getBalance(string $pubKey): float
    {
        return $this->client->call('getBalance', [$pubKey])['value'];
    }

    /**
     * @param string $transactionSignature
     * @return array|null
     */
    public function getConfirmedTransaction(string $transactionSignature): array|null
    {
        return $this->client->call('getConfirmedTransaction', [$transactionSignature]);
    }

    /**
     * NEW: This method is only available in solana-core v1.7 or newer. Please use getConfirmedTransaction for solana-core v1.6
     *
     * @param string $transactionSignature
     * @return array|null
     */
    public function getTransaction(string $transactionSignature): array|null
    {
        return $this->client->call('getTransaction', [$transactionSignature]);
    }

    /**
     * @param Commitment|null $commitment
     * @return array
     * @throws Exceptions\GenericException|Exceptions\MethodNotFoundException|Exceptions\InvalidIdResponseException
     */
    public function getRecentBlockhash(?Commitment $commitment = null): array
    {
        return $this->client->call('getRecentBlockhash', array_filter([$commitment]))['value'];
    }

    /**
     * @param Transaction $transaction
     * @param Keypair[] $signers
     * @param array $params
     * @return array|\Illuminate\Http\Client\Response
     * @throws Exceptions\GenericException
     * @throws Exceptions\InvalidIdResponseException
     * @throws Exceptions\MethodNotFoundException
     */
    public function sendTransaction(Transaction $transaction, array $signers, array $params = [])
    {
        if (! $transaction->recentBlockhash) {
            $transaction->recentBlockhash = $this->getRecentBlockhash()['blockhash'];
        }

        $transaction->sign(...$signers);

        $rawBinaryString = $transaction->serialize(false);

        $hashString = sodium_bin2base64($rawBinaryString, SODIUM_BASE64_VARIANT_ORIGINAL);

        $send_params = ['encoding' => 'base64', 'preflightCommitment' => 'confirmed'];
        if (!is_array($params))
            $params = [];
        foreach ($params as $k=>$v)
            $send_params[$k] = $v;
        
        return $this->client->call('sendTransaction', [$hashString, $send_params]);
    }
    
    
    /**
	 * @param Transaction $transaction
	 * @param Keypair[] $signers
	 * @param array $params
	 * @return array|\Illuminate\Http\Client\Response
	 * @throws Exceptions\GenericException
	 * @throws Exceptions\InvalidIdResponseException
	 * @throws Exceptions\MethodNotFoundException
	 */
	public function simulateTransaction(Transaction $transaction, array $signers, array $params = [])
	{
		$transaction->sign(...$signers);
		
		$rawBinaryString = $transaction->serialize(false);
		
		$hashString = sodium_bin2base64($rawBinaryString, SODIUM_BASE64_VARIANT_ORIGINAL);
		
		$send_params = ['encoding' => 'base64', 'commitment' => 'confirmed', 'sigVerify'=>true];
		if (!is_array($params))
			$params = [];
		foreach ($params as $k=>$v)
			$send_params[$k] = $v;
		
		return $this->client->call('simulateTransaction', [$hashString, $send_params]);
	}
    
}
