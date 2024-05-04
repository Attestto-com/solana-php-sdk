<?php

namespace Attestto\SolanaPhpSdk\Programs\SplToken\Actions;

use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\Exceptions\TokenAccountNotFoundError;
use Attestto\SolanaPhpSdk\Exceptions\TokenInvalidAccountOwnerError;
use Attestto\SolanaPhpSdk\Exceptions\TokenInvalidMintError;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\State\Account;
use Attestto\SolanaPhpSdk\Transaction;
use Attestto\SolanaPhpSdk\Util\Commitment;
use Attestto\SolanaPhpSdk\Util\ConfirmOptions;
use Attestto\SolanaPhpSdk\Util\Signer;
use Exception;
use function Attestto\SolanaPhpSdk\Programs\SplToken\getAccount;

trait SPLTokenActions {

    /**
     * @param Connection $connection
     * @param Signer $payer
     * @param PublicKey $mint
     * @param PublicKey $owner
     * @param false $allowOwnerOffCurve
     * @param Commitment|null $commitment
     * @param ConfirmOptions|null $confirmOptions
     * @param $programId
     * @param $associatedTokenProgramId
     * @return mixed
     * @throws Exception
     */
    public function getOrCreateAssociatedTokenAccount(
        Connection     $connection,
        Signer         $payer,
        PublicKey      $mint,
        PublicKey      $owner,
        false          $allowOwnerOffCurve = false,
        Commitment     $commitment = null,
        ConfirmOptions $confirmOptions = null,
                       $programId = null,
                       $associatedTokenProgramId = null
    ): mixed
    {
        if (!$programId){
            $programId = $this->SOLANA_TOKEN_PROGRAM_ID;
        }
        if (!$associatedTokenProgramId){
            $associatedTokenProgramId = $this->SOLANA_TOKEN_PROGRAM_ID;
        }


        $associatedToken = $this->getAssociatedTokenAddressSync(
            $mint,
            $owner,
            $allowOwnerOffCurve,
            $programId,
            $associatedTokenProgramId
        );

        try {
            $account = Account::getAccount($connection, $associatedToken, $commitment, $programId);
        } catch (Exception $error) {
            if ($error instanceof TokenAccountNotFoundError || $error instanceof TokenInvalidAccountOwnerError) {
                try {
                    $transaction = new Transaction();
                    $transaction->add(
                        $this->createAssociatedTokenAccountInstruction(
                            $payer->publicKey,
                            $associatedToken,
                            $owner,
                            $mint,
                            $programId,
                            $associatedTokenProgramId
                        )
                    );
                    // TODO Send and confirm transaction
                    //sendAndConfirmTransaction($connection, $transaction, [$payer], $confirmOptions);
                } catch (Exception $error) {
                    // Ignore all errors
                }

                $account = Account::getAccount($connection, $associatedToken, $commitment, $programId);
            } else {
                throw $error;
            }
        }

        if (!$account->mint->equals($mint)) throw new TokenInvalidMintError();
        if (!$account->owner->equals($owner)) throw new TokenInvalidAccountOwnerError();

        return $account;
    }





}