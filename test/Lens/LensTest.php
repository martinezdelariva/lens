<?php
/**
 * (c) José Luis Martínez de la Riva <martinezdelariva@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE file
 *  that was distributed with this source code.
 */

declare(strict_types=1);

namespace Martinezdelariva\Test\Lens;

use Martinezdelariva\Lens\ComposeLens;
use Martinezdelariva\Lens\MethodLens;
use Martinezdelariva\Lens\PropertyLens;
use Martinezdelariva\Lens\Lens;
use PHPUnit\Framework\TestCase;

class LensTest extends TestCase
{
    /**
     * @dataProvider lensProvider
     *
     * @param object $object
     * @param Lens   $lens
     */
    public function test_identity($object, Lens $lens)
    {
        $this->assertEquals(
            $object,
            $lens->set($object, $lens->get($object))
        );
    }

    /**
     * @dataProvider lensProvider
     *
     * @param object $object
     * @param Lens   $lens
     */
    public function test_retention($object, Lens $lens)
    {
        $theValue    = 'custom_value';
        $otherObject = $lens->set($object, $theValue);

        $this->assertEquals($theValue, $lens->get($otherObject));
    }

    /**
     * @dataProvider lensProvider
     *
     * @param object $object
     * @param Lens   $lens
     */
    public function test_double_set($object, Lens $lens)
    {
        $otherObject = $lens->set($object, 'first');
        $otherObject = $lens->set($otherObject, 'second');

        $this->assertEquals('second', $lens->get($otherObject));
    }

    /**
     * @dataProvider lensProvider
     *
     * @param object $object
     * @param Lens   $lens
     */
    public function test_set_return_new_instance($object, Lens $lens)
    {
        $this->assertNotSame($object, $lens->set($object, 'the_foo'));
    }

    /**
     * @dataProvider lensUnknownProvider
     *
     * @param object $object
     * @param Lens   $lens
     */
    public function test_unknown($object, Lens $lens)
    {
        $this->assertNull($lens->get($object));
        $this->assertSame($object, $lens->set($object, 'value'));
    }

    public function lensProvider()
    {
        return [
            [
                'object' => new Person('John', 'Brown', '42', new Address('The Street', '29001')),
                'lens'   => PropertyLens::withPropertyName('name'),
            ],
            [
                'object' => new Person('John', 'Brown', '42', new Address('The Street', '29001')),
                'lens'   => MethodLens::withMethodNames('age', 'setAge'),
            ],
            [
                'object' => new Person('John', 'Brown', '42', new Address('The Street', '29001')),
                'lens'   => new ComposeLens(
                    PropertyLens::withPropertyName('address'),
                    PropertyLens::withPropertyName('street')
                ),
            ],
        ];
    }

    public function lensUnknownProvider()
    {
        return [
            [
                'object' => new Person('John', 'Brown', '42', new Address('The Street', '29001')),
                'lens'   => PropertyLens::withPropertyName('unknown'),
            ],
            [
                'object' => new Person('John', 'Brown', '42', new Address('The Street', '29001')),
                'lens'   => MethodLens::withMethodNames('unknown', 'unknown'),
            ],
            [
                'object' => new Person('John', 'Brown', '42', new Address('The Street', '29001')),
                'lens'   => new ComposeLens(
                    PropertyLens::withPropertyName('unknown'),
                    PropertyLens::withPropertyName('street')
                ),
            ],
        ];
    }
}

class Person
{
    private $name;
    private $surname;
    private $age;
    private $address;

    public function __construct(string $name, string $surname, string $age, Address $address)
    {
        $this->name    = $name;
        $this->surname = $surname;
        $this->age     = $age;
        $this->address = $address;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function surname(): string
    {
        return $this->surname;
    }

    public function age(): string
    {
        return $this->age;
    }

    public function setAge(string $age)
    {
        $this->age = $age;
    }

    public function address(): Address
    {
        return $this->address;
    }
}

class Address
{
    private $street;
    private $postalCode;

    public function __construct(string $street, string $postalCode)
    {
        $this->street     = $street;
        $this->postalCode = $postalCode;
    }

    public function street(): string
    {
        return $this->street;
    }

    public function postalCode(): string
    {
        return $this->postalCode;
    }
}
