<?php
/**
 * (c) José Luis Martínez de la Riva <martinezdelariva@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE file
 *  that was distributed with this source code.
 */

declare(strict_types=1);

namespace Martinezdelariva\Lens;

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
