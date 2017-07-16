<?php
/**
 * (c) José Luis Martínez de la Riva <martinezdelariva@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE file
 *  that was distributed with this source code.
 */

declare(strict_types=1);

namespace Martinezdelariva\Lens;

final class ComposeLens implements Lens
{
    /**
     * @var Lens
     */
    private $outer;

    /**
     * @var Lens
     */
    private $inner;

    public function __construct(Lens $outer, Lens $inner)
    {
        $this->outer = $outer;
        $this->inner = $inner;
    }

    /**
     * @inheritdoc
     */
    public function get($object)
    {
        if ($outerValue = $this->outer->get($object)) {
            return $this->inner->get($outerValue);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function set($object, $value)
    {

        if (! $outerValue = $this->outer->get($object)) {
            return $object;
        }

        $innerValue = $this->inner->set($outerValue, $value);
        $newObject  = $this->outer->set($object, $innerValue);

        return $newObject;
    }
}
