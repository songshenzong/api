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

use Songshenzong\Log\DataCollector\ExceptionsCollector;
use Songshenzong\Log\DataCollector\MemoryCollector;
use Songshenzong\Log\DataCollector\MessagesCollector;
use Songshenzong\Log\DataCollector\PhpInfoCollector;
use Songshenzong\Log\DataCollector\RequestCollector;
use Songshenzong\Log\DataCollector\TimeDataCollector;

/**
 * Debug bar subclass which adds all included collectors
 */
class StandardDebugBar extends DebugBar
{
    public function __construct()
    {
        $this->addCollector(new PhpInfoCollector());
        $this->addCollector(new MessagesCollector());
        $this->addCollector(new RequestCollector());
        $this->addCollector(new TimeDataCollector());
        $this->addCollector(new MemoryCollector());
        $this->addCollector(new ExceptionsCollector());
    }
}
