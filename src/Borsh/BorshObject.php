<?php

namespace Attestto\SolanaPhpSdk\Borsh;

trait BorshObject
{
    use BorshDeserializable;
    use BorshSerializable;

    /**
     * @var array Holds dynamic properties
     */
    public $fields = [];


}
