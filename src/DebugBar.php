<?php
/*
 * This file is part of the package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Songshenzong\Log;

use ArrayAccess;
use Songshenzong\Log\DataCollector\DataCollectorInterface;
use Songshenzong\Log\Storage\StorageInterface;

/**
 *
 * Manages data collectors. DebugBar provides an array-like access
 * to collectors by name.
 *
 * <code>
 *     $debugbar = new DebugBar();
 *     $debugbar->addCollector(new DataCollector\MessagesCollector());
 *     $debugbar['messages']->addMessage("foobar");
 * </code>
 */
class DebugBar implements ArrayAccess
{

    protected $collectors = array();

    protected $data;


    protected $storage;

    protected $httpDriver;

    protected $stackSessionNamespace = 'PHPDEBUGBAR_STACK_DATA';

    protected $stackAlwaysUseSessionStorage = false;

    /**
     * Adds a data collector
     *
     * @param DataCollectorInterface $collector
     *
     * @throws DebugBarException
     * @return $this
     */
    public function addCollector(DataCollectorInterface $collector)
    {
        if ($collector -> getName() === '__meta') {
            throw new DebugBarException("'__meta' is a reserved name and cannot be used as a collector name");
        }
        if (isset($this -> collectors[$collector -> getName()])) {
            throw new DebugBarException("'{$collector->getName()}' is already a registered collector");
        }
        $this -> collectors[$collector -> getName()] = $collector;
        return $this;
    }

    /**
     * Checks if a data collector has been added
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasCollector($name)
    {
        return isset($this -> collectors[$name]);
    }

    /**
     * Returns a data collector
     *
     * @param string $name
     *
     * @return DataCollectorInterface
     * @throws DebugBarException
     */
    public function getCollector($name)
    {
        if (!isset($this -> collectors[$name])) {
            throw new DebugBarException("'$name' is not a registered collector");
        }
        return $this -> collectors[$name];
    }

    /**
     * Returns an array of all data collectors
     *
     * @return array[DataCollectorInterface]
     */
    public function getCollectors()
    {
        return $this -> collectors;
    }


    /**
     * Checks if the data will be persisted
     *
     * @return boolean
     */
    public function isDataPersisted()
    {
        return $this -> storage !== null;
    }

    /**
     * Sets the HTTP driver
     *
     * @param HttpDriverInterface $driver
     *
     * @return $this
     */
    public function setHttpDriver(HttpDriverInterface $driver)
    {
        $this -> httpDriver = $driver;
        return $this;
    }

    /**
     * Returns the HTTP driver
     *
     * If no http driver where defined, a PhpHttpDriver is automatically created
     *
     * @return HttpDriverInterface
     */
    public function getHttpDriver()
    {
        if ($this -> httpDriver === null) {
            $this -> httpDriver = new PhpHttpDriver();
        }
        return $this -> httpDriver;
    }


    /**
     * Returns collected data
     *
     * Will collect the data if none have been collected yet
     *
     * @return array
     */
    public function getData()
    {
        if ($this -> data === null) {
            $this -> collect();
        }
        return $this -> data;
    }


    /**
     * Checks if there is stacked data in the session
     *
     * @return boolean
     */
    public function hasStackedData()
    {
        try {
            $http = $this -> initStackSession();
        } catch (DebugBarException $e) {
            return false;
        }
        return count($http -> getSessionValue($this -> stackSessionNamespace)) > 0;
    }

    /**
     * Returns the data stacked in the session
     *
     * @param boolean $delete Whether to delete the data in the session
     *
     * @return array
     */
    public function getStackedData($delete = true)
    {
        $http        = $this -> initStackSession();
        $stackedData = $http -> getSessionValue($this -> stackSessionNamespace);
        if ($delete) {
            $http -> deleteSessionValue($this -> stackSessionNamespace);
        }

        $datasets = array();
        if ($this -> isDataPersisted() && !$this -> stackAlwaysUseSessionStorage) {
            foreach ($stackedData as $id => $data) {
                $datasets[$id] = $this -> getStorage() -> get($id);
            }
        } else {
            $datasets = $stackedData;
        }

        return $datasets;
    }


    /**
     * Sets whether to only use the session to store stacked data even
     * if a storage is enabled
     *
     * @param boolean $enabled
     *
     * @return $this
     */
    public function setStackAlwaysUseSessionStorage($enabled = true)
    {
        $this -> stackAlwaysUseSessionStorage = $enabled;
        return $this;
    }

    /**
     * Checks if the session is always used to store stacked data
     * even if a storage is enabled
     *
     * @return boolean
     */
    public function isStackAlwaysUseSessionStorage()
    {
        return $this -> stackAlwaysUseSessionStorage;
    }

    /**
     * Initializes the session for stacked data
     *
     * @return HttpDriverInterface
     * @throws DebugBarException
     */
    protected function initStackSession()
    {
        $http = $this -> getHttpDriver();
        if (!$http -> isSessionStarted()) {
            throw new DebugBarException("Session must be started before using stack data in the songshenzong");
        }

        if (!$http -> hasSessionValue($this -> stackSessionNamespace)) {
            $http -> setSessionValue($this -> stackSessionNamespace, array());
        }

        return $http;
    }



    // --------------------------------------------
    // ArrayAccess implementation

    public function offsetSet($key, $value)
    {
        throw new DebugBarException("Songshenzong[] is read-only");
    }

    public function offsetGet($key)
    {
        return $this -> getCollector($key);
    }

    public function offsetExists($key)
    {
        return $this -> hasCollector($key);
    }

    public function offsetUnset($key)
    {
        throw new DebugBarException("Songshenzong[] is read-only");
    }
}
