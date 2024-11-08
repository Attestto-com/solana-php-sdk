<?php

namespace Attestto\SolanaPhpSdk;

use Attestto\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Attestto\SolanaPhpSdk\Exceptions\GenericException;
use Attestto\SolanaPhpSdk\Exceptions\InvalidIdResponseException;
use Attestto\SolanaPhpSdk\Exceptions\MethodNotFoundException;
use Attestto\SolanaPhpSdk\Util\Commitment;
use Illuminate\Http\Client\Response;
use Psr\Http\Client\ClientExceptionInterface;
use SodiumException;

/**
 * Class Connection
 * @package Attestto\SolanaPhpSdk
 * https://solana-labs.github.io/solana-web3.js/v1.x/classes/Connection.html
 */
class Connection extends Program
{
    /**
     * @param string $pubKey
     * @return array
     */
    public function getAccountInfo(string $pubKey): array
    {
        $accountResponse = $this->client->call('getAccountInfo', [$pubKey, ["encoding" => "base64"]])['value'];

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
     * Deprecated: Use getLatestBlockhash instead
     */
    public function getRecentBlockhash(?Commitment $commitment = null): array
    {
        return $this->client->call('getLatestBlockhash', array_filter([$commitment]))['value'];
    }

    /**
     * @param string|null $commitment
     * @return array
     * @throws Exceptions\GenericException|Exceptions\MethodNotFoundException|Exceptions\InvalidIdResponseException|\Psr\Http\Client\ClientExceptionInterface
     */
    public function getLatestBlockhash(?Commitment $commitment = null): array
    {
        return $this->client->call('getLatestBlockhash', array_filter([$commitment]))['value'];
    }

    /**
     * @param Transaction $transaction
     * @param Keypair[] $signers
     * @param array $params

     * @throws GenericException
     * @throws InvalidIdResponseException
     * @throws MethodNotFoundException
     * @throws ClientExceptionInterface
     * @throws SodiumException
     * TODO Add Support for Versioned TXns
     */
    public function sendTransaction(Transaction $transaction, array $signers, array $params = [])
    {
        if (! $transaction->recentBlockhash) {
            $transaction->recentBlockhash = $this->getLatestBlockhash()['blockhash'];
        }
        $transaction->sign(...$signers);

        $rawBinaryString = $transaction->serialize(false);

        $hashString = sodium_bin2base64($rawBinaryString, SODIUM_BASE64_VARIANT_ORIGINAL);

        $send_params = ['encoding' => 'base64', 'preflightCommitment' => 'confirmed'];

        foreach ($params as $k=>$v)
            $send_params[$k] = $v;

        return $this->client->call('sendTransaction', [$hashString, $send_params]);
    }

    /**
	 * @param Transaction $transaction
	 * @param Keypair[] $signers
	 * @param array $params
	 * @return array|Response
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

		foreach ($params as $k=>$v)
			$send_params[$k] = $v;

		return $this->client->call('simulateTransaction', [$hashString, $send_params]);
	}

    /**
     * @param string $pubKey
     * @param array $params
     * @return string
     */
    public function requestAirdrop(array $params = []): string
    {
        return $response = $this->client->call('requestAirdrop', $params );

    }
    // https://solana.com/docs/rpc/http/getprogramaccounts
    // https://sns.guide/domain-name/all-domains.html
    public function getProgramAccounts(string $programIdBs58, $dataSlice, $filters)
    {
        $params = [
                $programIdBs58,
                [
                    'dataSlice' => $dataSlice,
                    'filters' => $filters,
                    'dataSize' => 108, // 'dataSize' => 108
                    'encoding' => 'base64',
                    'page' => 1,
                    'limit' => 1000

                ],


        ];
        return $this->client->call('getProgramAccounts', $params );
        //return $this->client->call('getAssetsByOwner', $params );

    }

    public function getMinimumBalanceForRentExemption(int $space = 1024){
        return $this->client->call('getMinimumBalanceForRentExemption', [$space] );
    }

}
