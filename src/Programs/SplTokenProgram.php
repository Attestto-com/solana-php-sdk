<?php

namespace Attestto\SolanaPhpSdk\Programs;

use Attestto\SolanaPhpSdk\Connection;
use Attestto\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Attestto\SolanaPhpSdk\Exceptions\GenericException;
use Attestto\SolanaPhpSdk\Exceptions\InputValidationException;
use Attestto\SolanaPhpSdk\Exceptions\InvalidIdResponseException;
use Attestto\SolanaPhpSdk\Exceptions\MethodNotFoundException;
use Attestto\SolanaPhpSdk\Exceptions\TokenAccountNotFoundError;
use Attestto\SolanaPhpSdk\Exceptions\TokenInvalidAccountOwnerError;
use Attestto\SolanaPhpSdk\Exceptions\TokenInvalidMintError;
use Attestto\SolanaPhpSdk\Exceptions\TokenOwnerOffCurveError;
use Attestto\SolanaPhpSdk\Keypair;
use Attestto\SolanaPhpSdk\Program;
use Attestto\SolanaPhpSdk\Programs\SplToken\Actions\SPLTokenActions;
use Attestto\SolanaPhpSdk\Programs\SplToken\Instructions\SPLTokenInstructions;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\State\Account;
use Attestto\SolanaPhpSdk\Transaction;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\Util\Buffer;
use Attestto\SolanaPhpSdk\Util\Commitment;
use Attestto\SolanaPhpSdk\Util\ConfirmOptions;
use Attestto\SolanaPhpSdk\Util\Signer;
use Exception;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * @property $SOLANA_TOKEN_PROGRAM_ID
 */
class SplTokenProgram extends Program
{
    public const TOKEN_PROGRAM_ID = 'TokenkegQfeZyiNwAJbNbGKPFXCWuBvf9Ss623VQ5DA';
    public const NATIVE_MINT = 'So11111111111111111111111111111111111111112';
    public const ASSOCIATED_TOKEN_PROGRAM_ID = 'ATokenGPvbdGVxr1b2hvZbsiqW5xWH25efTNsLJA8knL';
    public const TOKEN_2022_PROGRAM_ID ='TokenzQdBNbLqP5VEhdkAS6EPFLC1PHnBqCXEpPxuEb';
    public const TOKEN_2022_MINT = '9pan9bMn5HatX4EJdBwg9VgCa7Uz5HL8N1m5D3NdXejP';

    use SPLTokenActions;
    use SPLTokenInstructions;

    /**
     * @param string $pubKey
     * @return mixed
     */
    public function getTokenAccountsByOwner(string $pubKey)
    {
        return $this->client->call('getTokenAccountsByOwner', [
            $pubKey,
            [
                'programId' => self::TOKEN_PROGRAM_ID,
            ],
            [
                'encoding' => 'jsonParsed',
            ],
        ]);
    }

    /**
     * @param PublicKey $mint
     * @param PublicKey $owner
     * @param bool $allowOwnerOffCurve
     * @param string|null $programId
     * @param null $associatedTokenProgramId
     * @return PublicKey
     * @throws TokenOwnerOffCurveError
     * @throws InputValidationException
     */
    public function getAssociatedTokenAddressSync(
        PublicKey $mint,
        PublicKey $owner,
        bool      $allowOwnerOffCurve = false,
        PublicKey    $programId = new PublicKey(self::TOKEN_PROGRAM_ID),
                  PublicKey $atPid = new PublicKey(self::ASSOCIATED_TOKEN_PROGRAM_ID)
    ): PublicKey {
        if (!$allowOwnerOffCurve && !PublicKey::isOnCurve($owner->toBinaryString())) {
            throw new TokenOwnerOffCurveError();
        }



        $address = PublicKey::findProgramAddressSync(
            [$owner->toBuffer(), $programId->toBuffer(), $mint->toBuffer()],
            $atPid
        );

        return $address[0];
    }

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
