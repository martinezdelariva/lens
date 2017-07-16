<?php
/**
 * (c) José Luis Martínez de la Riva <martinezdelariva@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE file
 *  that was distributed with this source code.
 */

declare(strict_types=1);

namespace Martinezdelariva\Lens;

class PropertyLens implements Lens
{
    /**
     * @var string
     */
    private $property;

    public static function withPropertyName(string $property)
    {
        return new self($property);
    }

    private function __construct(string $property)
    {
        $this->property = $property;
    }

    /**
     * @inheritdoc
     */
    public function get($object)
    {
        $property = $this->property;
        $closure  = function () use ($property) {
            return $this->$property ?? null;
        };

        return $closure->call($object);
    }

    /**
     * @inheritdoc
     */
    public function set($object, $value)
    {
        $property = $this->property;
        $clone    = clone($object);

        $closure = function ($value) use ($property) {
            if (! property_exists($this, $property)) {
                return false;
            }

            $this->$property = $value;

            return true;
        };

        return $closure->call($clone, $value) ? $clone : $object;
    }
}
