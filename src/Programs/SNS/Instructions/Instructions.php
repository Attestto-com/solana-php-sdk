<?php

namespace Attestto\SolanaPhpSdk\Programs\SNS\Instructions;



use Attestto\SolanaPhpSdk\Exceptions\InputValidationException;
use Attestto\SolanaPhpSdk\PublicKey;

use Attestto\SolanaPhpSdk\TransactionInstruction;
use Attestto\SolanaPhpSdk\Util\Buffer;

trait Instructions {


    /**
     * @param PublicKey $nameProgramId
     * @param PublicKey $systemProgramId
     * @param PublicKey $nameKey
     * @param PublicKey $nameOwnerKey
     * @param PublicKey $payerKey
     * @param Buffer $hashed_name
     * @param $lamports
     * @param  $space
     * @param PublicKey|null $nameClassKey
     * @param PublicKey|null $nameParent
     * @param PublicKey|null $nameParentOwner
     * @return TransactionInstruction
     * @throws InputValidationException
     * @throws InputValidationException
     * @throws InputValidationException
     * @throws InputValidationException
     */
    function createInstruction(
        PublicKey  $nameProgramId,
        PublicKey  $systemProgramId,
        PublicKey  $nameKey,
        PublicKey  $nameOwnerKey,
        PublicKey  $payerKey,
        Buffer     $hashed_name,
        Buffer     $lamports,
        Buffer     $space,
        ?PublicKey $nameClassKey = null,
        ?PublicKey $nameParent = null,
        ?PublicKey $nameParentOwner = null
    ): TransactionInstruction {
        $buffers = [
            Buffer::fromArray([0]), // Create Instruction code 0
            new Buffer(count($hashed_name), Buffer::TYPE_INT, false),
            $hashed_name,
            $lamports,
            $space
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

        return new TransactionInstruction(
            new PublicKey($nameProgramId),
            $keys,
            $data
        );
    }


    /**
     * Updates an instruction.
     *
     * @param PublicKey $nameProgramId The public key of the name program.
     * @param PublicKey $nameAccountKey The public key of the name account.
     * @param  $offset The offset.
     * @param Buffer $input_data The input data.
     * @param PublicKey $nameUpdateSigner The public key of the name update signer.
     * @return TransactionInstruction The created transaction instruction.
     * @throws InputValidationException
     */
    function updateInstruction(
        PublicKey $nameProgramId,
        PublicKey $nameAccountKey,
        Buffer $offset,
        Buffer $input_data,
        PublicKey $nameUpdateSigner
    ): TransactionInstruction {
        $buffers = [
            Buffer::fromArray([1]),
            $offset,
            new Buffer(count($input_data), Buffer::TYPE_INT, false),
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

        return new TransactionInstruction(
            new PublicKey($nameProgramId),
            $keys,
            $data
        );
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
     * @throws InputValidationException
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

        return new TransactionInstruction(
            new PublicKey($nameProgramId),
            $keys,
            $data
        );
    }


    /**
     * Creates a realloc instruction.
     *
     * @param PublicKey $nameProgramId The public key of the name program.
     * @param PublicKey $systemProgramId The public key of the system program.
     * @param PublicKey $payerKey The public key of the payer.
     * @param PublicKey $nameAccountKey The public key of the name account.
     * @param PublicKey $nameOwnerKey The public key of the name owner.
     * @param Buffer $space A Buffer instance that should represent a 32-bit unsigned integer.
     * @return TransactionInstruction The created transaction instruction.
     * @throws InputValidationException
     */
    function reallocInstruction(
        PublicKey $nameProgramId,
        PublicKey $systemProgramId,
        PublicKey $payerKey,
        PublicKey $nameAccountKey,
        PublicKey $nameOwnerKey,
        Buffer $space
    ): TransactionInstruction {
        $buffers = [
            Buffer::fromArray([4]),
            $space
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

        return new TransactionInstruction(
            new PublicKey($nameProgramId),
            $keys,
            $data
        );
    }

    /**
     * Creates a delete instruction.
     *
     * @param PublicKey $nameProgramId The public key of the name program.
     * @param PublicKey $nameAccountKey The public key of the name account.
     * @param PublicKey $refundTargetKey The public key of the refund target.
     * @param PublicKey $nameOwnerKey The public key of the name owner.
     * @return TransactionInstruction The created transaction instruction.
     * @throws InputValidationException
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

        return new TransactionInstruction(
            new PublicKey($nameProgramId),
            $keys,
            $data
        );
    }


}
