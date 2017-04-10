<?php
/*
 * This file is part of the package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Songshenzong\Log\DataCollector;

/**
 * Collects array data
 */
class ConfigCollector extends DataCollector
{
    protected $name;

    protected $data;

    /**
     * @param array  $data
     * @param string $name
     */
    public function __construct(array $data = array(), $name = 'config')
    {
        $this -> name = $name;
        $this -> data = $data;
    }

    /**
     * Sets the data
     *
     * @param array $data
     */
    public function setData(array $data)
    {
        $this -> data = $data;
    }

    /**
     * @return array
     */
    public function collect()
    {
        return $this -> data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this -> name;
    }


}
