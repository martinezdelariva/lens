<?php
/**
 * (c) José Luis Martínez de la Riva <martinezdelariva@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE file
 *  that was distributed with this source code.
 */

declare(strict_types=1);

namespace Martinezdelariva\Lens;

final class MethodLens implements Lens
{
    /**
     * @var string
     */
    private $getter;

    /**
     * @var string
     */
    private $setter;

    public static function withMethodNames(string $getter, string $setter)
    {
        return new self($getter, $setter);
    }

    private function __construct(string $getter, string $setter)
    {
        $this->getter = $getter;
        $this->setter = $setter;
    }

    /**
     * @param object $object
     *
     * @return mixed
     */
    public function get($object)
    {
        if (! method_exists($object, $this->getter)) {
            return null;
        }

        return $object->{$this->getter}();
    }

    /**
     * @param object $object
     * @param mixed  $value
     *
     * @return object
     */
    public function set($object, $value)
    {
        if (! method_exists($object, $this->setter)) {
            return $object;
        }

        $newObject = clone ($object);
        $newObject->{$this->setter}($value);

        return $newObject;
    }
}
