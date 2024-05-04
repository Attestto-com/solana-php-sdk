<?php

namespace Attestto\config\SNS;

use Attestto\SolanaPhpSdk\Programs\SNS\Attestto;
use Attestto\SolanaPhpSdk\Programs\SNS\Buffer;
use Attestto\SolanaPhpSdk\Programs\SNS\Numberu32;
use Attestto\SolanaPhpSdk\Programs\SNS\Numberu64;
use Attestto\SolanaPhpSdk\PublicKey;
use Attestto\SolanaPhpSdk\SystemProgram;
use Attestto\SolanaPhpSdk\TransactionInstruction;

class SnsInstruction {


    use Attestto\SolanaPhpSdk\Buffer;
    use Attestto\SolanaPhpSdk\Numberu32;
    use Attestto\SolanaPhpSdk\Numberu64;
    use Attestto\SolanaPhpSdk\PublicKey;
    use Attestto\SolanaPhpSdk\TransactionInstruction;

    /**
     * Creates a transaction instruction.
     *
     * @param PublicKey $nameProgramId The public key of the name program.
     * @param PublicKey $systemProgramId The public key of the system program.
     * @param PublicKey $nameKey The public key of the name.
     * @param PublicKey $nameOwnerKey The public key of the name owner.
     * @param PublicKey $payerKey The public key of the payer.
     * @param Buffer $hashed_name The hashed name.
     * @param Numberu64 $lamports The amount of lamports.
     * @param Numberu32 $space The amount of space.
     * @param PublicKey|null $nameClassKey The public key of the name class.
     * @param PublicKey|null $nameParent The public key of the name parent.
     * @param PublicKey|null $nameParentOwner The public key of the name parent owner.
     * @return TransactionInstruction The created transaction instruction.
     */
    function createInstruction(
        PublicKey $nameProgramId,
        PublicKey $systemProgramId,
        PublicKey $nameKey,
        PublicKey $nameOwnerKey,
        PublicKey $payerKey,
        Buffer $hashed_name,
        Numberu64 $lamports,
        Numberu32 $space,
        ?PublicKey $nameClassKey = null,
        ?PublicKey $nameParent = null,
        ?PublicKey $nameParentOwner = null
    ): TransactionInstruction {
        $buffers = [
            Buffer::fromArray([0]),
            (new Numberu32($hashed_name->length))->toBuffer(),
            $hashed_name,
            $lamports->toBuffer(),
            $space->toBuffer()
        ];

        $data = Buffer::concat($buffers);

        $keys = [
            [
                'pubkey' => $systemProgramId,
                'isSigner' => false,
                'isWritable' => false
            ],
            [
                'pubkey' => $payerKey,
                'isSigner' => true,
                'isWritable' => true
            ],
            [
                'pubkey' => $nameKey,
                'isSigner' => false,
                'isWritable' => true
            ],
            [
                'pubkey' => $nameOwnerKey,
                'isSigner' => false,
                'isWritable' => false
            ]
        ];

        if ($nameClassKey) {
            $keys[] = [
                'pubkey' => $nameClassKey,
                'isSigner' => true,
                'isWritable' => false
            ];
        } else {
            $keys[] = [
                'pubkey' => new PublicKey(Buffer::alloc(32)),
                'isSigner' => false,
                'isWritable' => false
            ];
        }

        if ($nameParent) {
            $keys[] = [
                'pubkey' => $nameParent,
                'isSigner' => false,
                'isWritable' => false
            ];
        } else {
            $keys[] = [
                'pubkey' => new PublicKey(Buffer::alloc(32)),
                'isSigner' => false,
                'isWritable' => false
            ];
        }

        if ($nameParentOwner) {
            $keys[] = [
                'pubkey' => $nameParentOwner,
                'isSigner' => true,
                'isWritable' => false
            ];
        }

        return new TransactionInstruction([
            'keys' => $keys,
            'programId' => $nameProgramId,
            'data' => $data
        ]);
    }


    /**
     * Updates an instruction.
     *
     * @param PublicKey $nameProgramId The public key of the name program.
     * @param PublicKey $nameAccountKey The public key of the name account.
     * @param Numberu32 $offset The offset.
     * @param Buffer $input_data The input data.
     * @param PublicKey $nameUpdateSigner The public key of the name update signer.
     * @return TransactionInstruction The created transaction instruction.
     */
    function updateInstruction(
        PublicKey $nameProgramId,
        PublicKey $nameAccountKey,
        Numberu32 $offset,
        Buffer $input_data,
        PublicKey $nameUpdateSigner
    ): TransactionInstruction {
        $buffers = [
            Buffer::fromArray([1]),
            $offset->toBuffer(),
            (new Numberu32($input_data->length))->toBuffer(),
            $input_data
        ];

        $data = Buffer::concat($buffers);
        $keys = [
            [
                'pubkey' => $nameAccountKey,
                'isSigner' => false,
                'isWritable' => true
            ],
            [
                'pubkey' => $nameUpdateSigner,
                'isSigner' => true,
                'isWritable' => false
            ]
        ];

        return new TransactionInstruction([
            'keys' => $keys,
            'programId' => $nameProgramId,
            'data' => $data
        ]);
    }


    /**
     * Creates a transfer instruction.
     *
     * @param PublicKey $nameProgramId The public key of the name program.
     * @param PublicKey $nameAccountKey The public key of the name account.
     * @param PublicKey $newOwnerKey The public key of the new owner.
     * @param PublicKey $currentNameOwnerKey The public key of the current name owner.
     * @param PublicKey|null $nameClassKey The public key of the name class.
     * @param PublicKey|null $nameParent The public key of the name parent.
     * @param PublicKey|null $parentOwner The public key of the parent owner.
     * @return TransactionInstruction The created transaction instruction.
     */
    function transferInstruction(
        PublicKey $nameProgramId,
        PublicKey $nameAccountKey,
        PublicKey $newOwnerKey,
        PublicKey $currentNameOwnerKey,
        ?PublicKey $nameClassKey = null,
        ?PublicKey $nameParent = null,
        ?PublicKey $parentOwner = null
    ): TransactionInstruction {
        $buffers = [
            Buffer::fromArray([2]),
            $newOwnerKey->toBuffer()
        ];

        $data = Buffer::concat($buffers);
        $keys = [
            [
                'pubkey' => $nameAccountKey,
                'isSigner' => false,
                'isWritable' => true
            ],
            [
                'pubkey' => $parentOwner ? $parentOwner : $currentNameOwnerKey,
                'isSigner' => true,
                'isWritable' => false
            ]
        ];

        if ($nameClassKey) {
            $keys[] = [
                'pubkey' => $nameClassKey,
                'isSigner' => true,
                'isWritable' => false
            ];
        }

        if ($parentOwner && $nameParent) {
            if (!$nameClassKey) {
                $keys[] = [
                    'pubkey' => new PublicKey(Buffer::alloc(32)),
                    'isSigner' => false,
                    'isWritable' => false
                ];
            }
            $keys[] = [
                'pubkey' => $nameParent,
                'isSigner' => false,
                'isWritable' => false
            ];
        }

        return new TransactionInstruction([
            'keys' => $keys,
            'programId' => $nameProgramId,
            'data' => $data
        ]);
    }


    /**
     * Creates a realloc instruction.
     *
     * @param PublicKey $nameProgramId The public key of the name program.
     * @param PublicKey $systemProgramId The public key of the system program.
     * @param PublicKey $payerKey The public key of the payer.
     * @param PublicKey $nameAccountKey The public key of the name account.
     * @param PublicKey $nameOwnerKey The public key of the name owner.
     * @param Numberu32 $space The amount of space.
     * @return TransactionInstruction The created transaction instruction.
     */
    function reallocInstruction(
        PublicKey $nameProgramId,
        PublicKey $systemProgramId,
        PublicKey $payerKey,
        PublicKey $nameAccountKey,
        PublicKey $nameOwnerKey,
        Numberu32 $space
    ): TransactionInstruction {
        $buffers = [
            Buffer::fromArray([4]),
            $space->toBuffer()
        ];

        $data = Buffer::concat($buffers);
        $keys = [
            [
                'pubkey' => $systemProgramId,
                'isSigner' => false,
                'isWritable' => false
            ],
            [
                'pubkey' => $payerKey,
                'isSigner' => true,
                'isWritable' => true
            ],
            [
                'pubkey' => $nameAccountKey,
                'isSigner' => false,
                'isWritable' => true
            ],
            [
                'pubkey' => $nameOwnerKey,
                'isSigner' => true,
                'isWritable' => false
            ]
        ];

        return new TransactionInstruction([
            'keys' => $keys,
            'programId' => $nameProgramId,
            'data' => $data
        ]);
    }

    /**
     * Creates a delete instruction.
     *
     * @param PublicKey $nameProgramId The public key of the name program.
     * @param PublicKey $nameAccountKey The public key of the name account.
     * @param PublicKey $refundTargetKey The public key of the refund target.
     * @param PublicKey $nameOwnerKey The public key of the name owner.
     * @return TransactionInstruction The created transaction instruction.
     */
    function deleteInstruction(
        PublicKey $nameProgramId,
        PublicKey $nameAccountKey,
        PublicKey $refundTargetKey,
        PublicKey $nameOwnerKey
    ): TransactionInstruction {
        $buffers = [
            Buffer::fromArray([3])
        ];

        $data = Buffer::concat($buffers);
        $keys = [
            [
                'pubkey' => $nameAccountKey,
                'isSigner' => false,
                'isWritable' => true
            ],
            [
                'pubkey' => $nameOwnerKey,
                'isSigner' => true,
                'isWritable' => false
            ],
            [
                'pubkey' => $refundTargetKey,
                'isSigner' => false,
                'isWritable' => true
            ]
        ];

        return new TransactionInstruction([
            'keys' => $keys,
            'programId' => $nameProgramId,
            'data' => $data
        ]);
    }


}
