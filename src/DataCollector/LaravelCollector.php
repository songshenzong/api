<?php

namespace Songshenzong\Log\DataCollector;

use Songshenzong\Log\DataCollector\DataCollector;
use Illuminate\Foundation\Application;

class LaravelCollector extends DataCollector
{
    /** @var \Illuminate\Foundation\Application $app */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app = null)
    {
        $this->app = $app;
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        // Fallback if not injected
        $app = $this->app ?: app();

        return [
            "version" => $app::VERSION,
            "environment" => $app->environment(),
            "locale" => $app->getLocale(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'laravel';
    }


}
