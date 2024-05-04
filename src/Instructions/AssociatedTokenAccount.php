<?php

use Attestto\SolanaPhpSdk\Exceptions\InputValidationException;
use Attestto\SolanaPhpSdk\Programs\SystemProgram;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\Util\Buffer;

trait AssociatedTokenAccount
{

    /**
     * @param PublicKey $payer
     * @param PublicKey $associatedToken
     * @param PublicKey $owner
     * @param PublicKey $mint
     * @param null $programId
     * @param null $associatedTokenProgramId
     * @return TransactionInstruction
     * @throws InputValidationException
     */
    public function createAssociatedTokenAccountInstruction(
        PublicKey $payer,
        PublicKey $associatedToken,
        PublicKey $owner,
        PublicKey $mint,
                  $programId = null,
                  $associatedTokenProgramId = null
    ): TransactionInstruction {
        if (!$programId){
            $programId = $this->SOLANA_TOKEN_PROGRAM_ID;
        }
        if (!$associatedTokenProgramId){
            $associatedTokenProgramId = $this->SOLANA_TOKEN_PROGRAM_ID;
        }

        return $this->buildAssociatedTokenAccountInstruction(
            $payer,
            $associatedToken,
            $owner,
            $mint,
            new Buffer([]),
            $programId,
            $associatedTokenProgramId
        );
    }

    /**
     * @param PublicKey $payer
     * @param PublicKey $associatedToken
     * @param PublicKey $owner
     * @param PublicKey $mint
     * @param Buffer    $instructionData
     * @param string|null $programId
     * @param string|null $associatedTokenProgramId
     * @return TransactionInstruction
     */
    public function buildAssociatedTokenAccountInstruction(
        PublicKey $payer,
        PublicKey $associatedToken,
        PublicKey $owner,
        PublicKey $mint,
        Buffer    $instructionData,
                  $programId = null,
                  $associatedTokenProgramId = null
    ): TransactionInstruction
    {
        if (!$programId) {
            $programId = $this->SOLANA_TOKEN_PROGRAM_ID;
        }
        if (!$associatedTokenProgramId) {
            $associatedTokenProgramId = $this->SOLANA_TOKEN_PROGRAM_ID;
        }

        $keys = [
            ['pubkey' => $payer, 'isSigner' => true, 'isWritable' => true],
            ['pubkey' => $associatedToken, 'isSigner' => false, 'isWritable' => true],
            ['pubkey' => $owner, 'isSigner' => false, 'isWritable' => false],
            ['pubkey' => $mint, 'isSigner' => false, 'isWritable' => false],
            ['pubkey' => SystemProgram::programId(), 'isSigner' => false, 'isWritable' => false],
            ['pubkey' => $programId, 'isSigner' => false, 'isWritable' => false],
        ];

        return new TransactionInstruction(
            $associatedTokenProgramId,
            $keys,
            $instructionData->data
        );
    }
}