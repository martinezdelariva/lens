# Lens

[![Build Status](https://travis-ci.org/martinezdelariva/lens.svg?branch=master)](https://travis-ci.org/martinezdelariva/lens)

Lens is an abstraction from functional programming which helps to deal with updating immutable data.

## Installation

Install it using [Composer](https://getcomposer.org/)


    composer require martinezdelariva/lens

## Motivation 
 
The most common approach in functional programming to mutate state is to create a new instance of the object with the updated values:
 
```php
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
    
    public function withStreet(string $street): self
    {
        return new self($street, $this->postalCode);
    }
}

$address = new Address('123 Foo St', '20900');
$address = $address->withAddress('123 Bar St');
```

This approach, although perfectly valid, it could face the following disadvantages:

- Tedious adding new method for each property able to update.
- Error prone creating manually new objects reusing same values but the new one.
- Nested objects calls proper _withX_ methods in cascade.

## Lens structure

- Lens per each property.
- A getter 
- A setter

```php
interface Lens
{
    /**
     * @param object $object
     *
     * @return mixed
     */
    public function get($object);

    /**
     * @param object $object
     * @param mixed $value
     *
     * @return object
     */
    public function set($object, $value);
}

```
    
## Examples

Having the following `Address` class, update the _street_ `Address`:

```php
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
    
    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }
}
```

#### Property strategy

Internally uses a closure bind to the given object which get/set the properties. So, the class to be updated not need setters.
This strategy does not check the object or property is the type expected, then in the following example we could use `$streetLens` for whatever class that has a property `$street` 

```php
$address    = new Address('123 Foo St', '20900');

$streetLens = PropertyLens::withPropertyName('street');
$newAddress = $addressStreetLens->set($address, '123 Bar St');

$address;    // ('street' => '123 Foo St', 'postalCode' => '20900')
$newAddress; // ('street' => '123 Bar St', 'postalCode' => '20900')
```

#### Method strategy

It uses method names given to get/set properties. 
This strategy works for whatever class that implements these methods. The property type checking could be done explicitly by the setter method implemented in the class.  

```php
$address        = new Address('123 Foo St', '20900');
$postalCodeLens = MethodLens::withMethodNames('postalCode', 'setPostalCode');
$newAddress     = $postalCodeLens->set($address, '18210');

$address;    // ('street' => '123 Foo St', 'postalCode' => '20900')
$newAddress; // ('street' => '123 Foo St', 'postalCode' => '18210')
```

#### Compose

To update nested structures.

```php
class Person
{
    private $name;
    private $address;

    public function __construct(string $name, Address $address)
    {
        $this->name    = $name;
        $this->address = $address;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function address(): Address
    {
        return $this->address;
    }
}

$addressStreetLens = new ComposeLens(
    PropertyLens::withPropertyName('address'),
    PropertyLens::withPropertyName('street')
);

$person    = new Person('John', new Address('123 Foo St', '20900'));
$newPerson = $addressStreetLens->set($person, '456 Bar St');

$person;    // ('name' => 'John', 'address' => Address('street' => '123 Foo St', 'postalCode' => '20900')
$newPerson; // ('name' => 'John', 'address' => Address('street' => '456 Bar St', 'postalCode' => '20900')
```

