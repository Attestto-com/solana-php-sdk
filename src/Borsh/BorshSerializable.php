<?php

namespace Attestto\SolanaPhpSdk\Borsh;

trait BorshSerializable
{
    /**
     * @param $name
     * @return mixed
     */

    /**
     * Magic getter to retrieve dynamically set properties.
     * Note, changed from dynamic properties to jeys of an Array due to Dynamic properties being deprecated.
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get(string $name)
    {
        return $this->fields[$name];
    }
}
