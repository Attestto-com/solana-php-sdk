<?php

namespace Attestto\SolanaPhpSdk\Tests\Unit;

use Attestto\SolanaPhpSdk\Borsh\Borsh;
use Attestto\SolanaPhpSdk\Borsh\BorshObject;
use Attestto\SolanaPhpSdk\Tests\TestCase;

class TestObject {
    use BorshObject;
}

class TestWithPrivateVariable {
    use BorshObject;

    private $m;

    public function setM($m) {$this->m = $m;}
    public function getM() {return $this->m;}
}

class TestWithConstructorParameters {
    use BorshObject;

    private $m;

    public function __construct($m)
    {
        $this->m = $m;
    }

    public function getM() {return $this->m;}

    public static function borshConstructor()
    {
        return new static(null);
    }
}

class BorshTest extends TestCase
{
    #[Test]
    public function test_it_serialize_object()
    {
        $value = new TestObject();
        $value->fields['x'] = 255;
        $value->fields['y'] = 20;
        $value->fields['z'] = '123';
        $value->fields['a'] = 12.987;
        $value->fields['b'] = -121;
        $value->fields['c'] = -20;
        $value->fields['q'] = [1, 2, 3];

        $schema = [
            TestObject::class => [
                'kind' => 'struct',
                'fields' => [
                    ['x', 'u8'],
                    ['y', 'u64'],
                    ['z', 'string'],
                    ['a', 'f64'],
                    ['b', 'i32'],
                    ['c', 'i8'],
                    ['q', [3]],
                ],
            ],
        ];

        $buffer = Borsh::serialize($schema, $value);
        $newValue = Borsh::deserialize($schema, TestObject::class, $buffer);

        $this->assertInstanceOf(TestObject::class, $newValue);
        $this->assertEquals(255, $newValue->fields['x']);
        $this->assertEquals(20, $newValue->fields['y']);
        $this->assertEquals('123', $newValue->fields['z']);
        $this->assertEquals(12.987, $newValue->fields['a']);
        $this->assertEquals(-121, $newValue->fields['b']);
        $this->assertEquals(-20, $newValue->fields['c']);
        $this->assertEquals([1, 2, 3], $newValue->fields['q']);
    }
    #[Test]
    public function test_it_serialize_optional_field()
    {
        $schema = [
            TestObject::class => [
                'kind' => 'struct',
                'fields' => [
                    ['x', [
                        'kind' => 'option',
                        'type' => 'string',
                    ]],
                ],
            ],
        ];

        $value = new TestObject();
        $value->x = 'bacon';
        $buffer = Borsh::serialize($schema, $value);
        $newValue = Borsh::deserialize($schema, TestObject::class, $buffer);
        $this->assertEquals('bacon', $newValue->x);

        $value = new TestObject();
        $value->fields['x'] = null;
        $buffer = Borsh::serialize($schema, $value);
        $newValue = Borsh::deserialize($schema, TestObject::class, $buffer);
        $this->assertNull($newValue->fields['x']);
    }

    #[Test]
    public function test_it_serialize_deserialize_fixed_array()
    {
        $schema = [
            TestObject::class => [
                'kind' => 'struct',
                'fields' => [
                    ['x', ['string', 2]],
                ],
            ],
        ];

        $value = new TestObject();
        $value->x = ['hello', 'world'];

        $buffer = Borsh::serialize($schema, $value);
        $newValue = Borsh::deserialize($schema, TestObject::class, $buffer);

        $this->assertEquals([5, 0, 0, 0, 104, 101, 108, 108, 111, 5, 0, 0, 0, 119, 111, 114, 108, 100], $buffer);
        // Note, asserts TRUE because of the magic getter __get()
        $this->assertEquals(['hello', 'world'], $newValue->x);
    }

    #[Test]
    public function test_it_serialize_deserialize_invisible_properties()
    {
        $value = new TestWithPrivateVariable();
        $value->setM(255);

        $schema = [
            TestWithPrivateVariable::class => [
                'kind' => 'struct',
                'fields' => [
                    ['m', 'u8'],
                ],
            ],
        ];

        $buffer = Borsh::serialize($schema, $value);
        $newValue = Borsh::deserialize($schema, TestWithPrivateVariable::class, $buffer);

        $this->assertInstanceOf(TestWithPrivateVariable::class, $newValue);
        $this->assertEquals(255, $newValue->getM());
    }

    #[Test]
    public function test_it_serialize_deserialize_handles_constructor_with_parameters()
    {
        $value = new TestWithConstructorParameters(255);

        $schema = [
            TestWithConstructorParameters::class => [
                'kind' => 'struct',
                'fields' => [
                    ['m', 'u8'],
                ],
            ],
        ];

        $buffer = Borsh::serialize($schema, $value);
        $newValue = Borsh::deserialize($schema, TestWithConstructorParameters::class, $buffer);

        $this->assertInstanceOf(TestWithConstructorParameters::class, $newValue);
        $this->assertEquals(255, $newValue->getM());
    }
}
