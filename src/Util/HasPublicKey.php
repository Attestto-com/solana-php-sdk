<?php

namespace Attestto\SolanaPhpSdk\Util;

use Attestto\SolanaPhpSdk\PublicKey;

interface HasPublicKey
{
    public function getPublicKey(): PublicKey;
}
