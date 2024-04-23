<?php

namespace attestto\SolanaPhpSdk\Util;

use attestto\SolanaPhpSdk\PublicKey;

interface HasPublicKey
{
    public function getPublicKey(): PublicKey;
}
