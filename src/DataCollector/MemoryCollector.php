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
 * Collects info about memory usage
 */
class MemoryCollector extends DataCollector
{
    protected $peakUsage = 0;

    /**
     * Returns the peak memory usage
     *
     * @return integer
     */
    public function getPeakUsage()
    {
        return $this->peakUsage;
    }

    /**
     * Updates the peak memory usage value
     */
    public function updatePeakUsage()
    {
        $this->peakUsage = memory_get_peak_usage(true);
    }

    /**
     * @return array
     */
    public function collect()
    {
        $this->updatePeakUsage();
        return array(
            'peak_usage' => $this->peakUsage,
            'peak_usage_str' => $this->getDataFormatter()->formatBytes($this->peakUsage)
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'memory';
    }


}
