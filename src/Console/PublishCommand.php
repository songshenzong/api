<?php

namespace Songshenzong\Log\Console;

use Illuminate\Console\Command;

/**
 * Publish the Debugbar assets to the public directory
 *
 * @author     Barry vd. Heuvel <barryvdh@gmail.com>
 * @deprecated No longer needed because of the AssetController
 */
class PublishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'songshenzong:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the Songshenzong assets';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this -> info(
            'OK'
        );
    }
}
