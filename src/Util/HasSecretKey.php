<?php

namespace attestto\SolanaPhpSdk\Util;

interface HasSecretKey
{
    public function getSecretKey(): Buffer;
}
