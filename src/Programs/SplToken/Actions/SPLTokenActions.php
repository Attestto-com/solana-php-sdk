<?php

namespace Attestto\SolanaPhpSdk\Programs\SplToken\Actions;

use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Attestto\SolanaPhpSdk\Exceptions\GenericException;
use Attestto\SolanaPhpSdk\Exceptions\InputValidationException;
use Attestto\SolanaPhpSdk\Exceptions\InvalidIdResponseException;
use Attestto\SolanaPhpSdk\Exceptions\MethodNotFoundException;
use Attestto\SolanaPhpSdk\Exceptions\TokenInvalidAccountOwnerError;
use Attestto\SolanaPhpSdk\Exceptions\TokenInvalidMintError;
use Attestto\SolanaPhpSdk\Exceptions\TokenOwnerOffCurveError;
use Attestto\SolanaPhpSdk\Keypair;
use Attestto\SolanaPhpSdk\Programs\SplToken\State\Account;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\Transaction;
use Attestto\SolanaPhpSdk\Util\Commitment;
use Attestto\SolanaPhpSdk\Util\ConfirmOptions;
use Attestto\SolanaPhpSdk\Util\Signer;
use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use function Attestto\SolanaPhpSdk\Programs\SplToken\getAccount;

trait SPLTokenActions {

    /**
     * @param Connection $connection
     * @param Signer|Keypair $payer
     * @param PublicKey $mint
     * @param PublicKey $owner
     * @param boolean $allowOwnerOffCurve
     * @param Commitment|null $commitment
     * @param ConfirmOptions $confirmOptions
     * @param PublicKey $programId
     * @param PublicKey $associatedTokenProgramId
     * @return mixed
     * @throws AccountNotFoundException
     * @throws ClientExceptionInterface
     * @throws InputValidationException
     * @throws TokenInvalidAccountOwnerError
     * @throws TokenInvalidMintError
     * @throws TokenOwnerOffCurveError
     * @throws GenericException
     * @throws InvalidIdResponseException
     * @throws MethodNotFoundException
     * @throws \SodiumException
     */
    public function getOrCreateAssociatedTokenAccount(
        Connection     $connection,
        mixed          $payer,
        PublicKey      $mint,
        PublicKey      $owner,
        bool           $allowOwnerOffCurve = true,
        Commitment     $commitment = null,
        ConfirmOptions $confirmOptions = null,
        PublicKey      $programId = new PublicKey(self::TOKEN_PROGRAM_ID),
        PublicKey      $associatedTokenProgramId = new PublicKey(self::ASSOCIATED_TOKEN_PROGRAM_ID)
    ): Account
    {

        $associatedToken = $this->getAssociatedTokenAddressSync(
            $mint,
            $owner,
            $allowOwnerOffCurve,
            $programId,
            $associatedTokenProgramId
        );
        $ata = $associatedToken->toBase58();
        try {
            $account = Account::getAccount($connection, $associatedToken, $commitment, $programId);
        } catch (Exception $error) {
            if ($error instanceof AccountNotFoundException || $error instanceof TokenInvalidAccountOwnerError) {
                try {
                    $transaction = new Transaction();
                    $transaction->add(
                        $this->createAssociatedTokenAccountInstruction(
                            $payer->getPublicKey(),
                            $associatedToken,
                            $owner,
                            $mint,
                            $programId,
                            $associatedTokenProgramId
                        )
                    );
                    if (!$confirmOptions) $confirmOptions = new ConfirmOptions();
                    $transaction->feePayer = $payer->getPublicKey();
                    $txnHash = $connection->sendTransaction( $transaction, [$payer]);
                } catch (Exception $error) {
                    // Ignore all errors
                    // Account Exists but is not funded
                    throw $error;
                }

                $account = Account::getAccount($connection, $associatedToken, $commitment, $programId);
            } else {
                throw $error;
            }
        }

        if ($account->mint != $mint) throw new TokenInvalidMintError(
            $account->mint->toBase58() . ' != ' . $mint->toBase58()
        );
        if ($account->owner != $owner) throw new TokenInvalidAccountOwnerError(
            $account->owner->toBase58() . ' != ' . $owner->toBase58()
        );

        return $account;
    }





}
