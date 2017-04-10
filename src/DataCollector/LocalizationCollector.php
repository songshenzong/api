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
 * Collects info about the current localization state
 */
class LocalizationCollector extends DataCollector
{
    /**
     * Get the current locale
     *
     * @return string
     */
    public function getLocale()
    {
        return setlocale(LC_ALL, 0);
    }

    /**
     * Get the current translations domain
     *
     * @return string
     */
    public function getDomain()
    {
        return textdomain();
    }

    /**
     * @return array
     */
    public function collect()
    {
        return array(
          'locale' => $this->getLocale(),
          'domain' => $this->getDomain(),
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'localization';
    }


}
