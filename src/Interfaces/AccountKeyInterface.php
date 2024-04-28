<?php

namespace Attestto\SolanaPhpSdk\Interfaces;

use Attestto\SolanaPhpSdk\PublicKey;

interface AccountKeyInterface {
    public function pubKey(): PublicKey;
    public function isSigner(): bool;
    public function isWritable(): bool;
}