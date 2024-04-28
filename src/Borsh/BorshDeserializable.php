<?php
namespace Attestto\SolanaPhpSdk\Borsh;

trait BorshDeserializable
{
    /**
     * Create a new instance of this object.
     *
     * Note: must override when the default constructor required parameters!
     *
     * @return $this
     */
    public static function borshConstructor()
    {
        return new static();
    }

    /**
     * @var array Holds dynamic properties
     */
    protected $fields = [];

    /**
     * Magic setter to dynamically set properties.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set(string $name, mixed $value)
    {
        $this->fields[$name] = $value;
    }


    /**
     * Magic isset to check if dynamically set property is set.
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name)
    {
        return isset($this->fields[$name]);
    }

    /**
     * Magic unset to unset dynamically set property.
     *
     * @param string $name
     */
    public function __unset(string $name)
    {
        unset($this->fields[$name]);
    }
}
