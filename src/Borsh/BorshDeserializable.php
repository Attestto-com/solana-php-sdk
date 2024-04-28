<?php
namespace Attestto\SolanaPhpSdk\Borsh;
use ReflectionClass;
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
     * Magic setter to dynamically set properties.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, mixed $value)
    {
        // Set the value in the dynamic properties if it's not private
        if (!$this->isPrivateProperty($name)) {
            $this->fields[$name] = $value;
        }

        // Check if the property exists as a private property
        if ($this->isPrivateProperty($name)) {
            // Use reflection to set the value of the private property
            $reflectionClass = new ReflectionClass($this);
            $property = $reflectionClass->getProperty($name);
            $property->setAccessible(true);
            $property->setValue($this, $value);
        }
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
        return isset($this->fields[$name]) || isset($this->private[$name]);
    }

    /**
     * Magic unset to unset dynamically set property.
     *
     * @param string $name
     */
    public function __unset(string $name)
    {
        if (isset($this->fields[$name])) {
            unset($this->fields[$name]);
        } elseif ( isset($this->private[$name])) {
            unset($this->privateProperties[$name]);
        }
    }

    /**
     * Determine if a property is considered private.
     *
     * @param string $name
     *
     * @return bool
     */
    private function isPrivateProperty(string $name): bool
    {
        // Get the class name ( whatever class is implementing this trait, e.g. Any Schema/Struct based object
        $className = static::class;

        // Create a ReflectionClass instance for the class
        $reflectionClass = new ReflectionClass($className);

        // Check if the property is declared in the class and is private
        return $reflectionClass->hasProperty($name) && $reflectionClass->getProperty($name)->isPrivate();
    }
}
