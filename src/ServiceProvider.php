<?php namespace Songshenzong\Log;

use Illuminate\Session\SessionManager;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    /**
     * True when enabled, false disabled an null for still unknown
     *
     * @var bool
     */
    protected $enabled = null;


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this -> app -> alias(
            'Songshenzong\Log\DataFormatter\DataFormatter',
            'Songshenzong\Log\DataFormatter\DataFormatterInterface'
        );

        $this -> app -> singleton('songshenzong', function ($app) {
            $debugbar = new LaravelDebugbar($app);

            if ($app -> bound(SessionManager::class)) {
                $sessionManager = $app -> make(SessionManager::class);
                $httpDriver     = new SymfonyHttpDriver($sessionManager);
                $debugbar -> setHttpDriver($httpDriver);
            }

            return $debugbar;
        }
        );

        $this -> app -> alias('songshenzong', 'Songshenzong\Log\LaravelDebugbar');

        $this -> app -> singleton('command.songshenzong.clear',
            function ($app) {
                return new Console\ClearCommand($app['songshenzong']);
            }
        );

        $this -> commands(['command.songshenzong.clear']);
    }


    /**
     * Check if is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        if ($this -> enabled === null) {
            $this -> enabled = (boolean)config('songshenzong.enabled');;
        }


        if ($this -> enabled === true) {
            $environments    = config('songshenzong.env', ['dev', 'local', 'production']);
            $this -> enabled = in_array(env('APP_ENV'), $environments);
        }

        return $this -> enabled;
    }


    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

        if (!$this -> isEnabled()) {
            return;
        }


        app('Illuminate\Contracts\Http\Kernel') -> pushMiddleware('Songshenzong\Log\Middleware');


        $routeConfig = [
            'namespace' => 'Songshenzong\Log\Controllers',
            'prefix'    => 'songshenzong',
        ];

        app('router') -> group($routeConfig, function ($router) {

            $router -> get('', 'WebController@index');

            $router -> group(['middleware' => 'Songshenzong\Log\TokenMiddleware'], function ($router) {

                $router -> get('logs', 'ApiController@getList');

                $router -> get('logs/{id}', 'ApiController@getData') -> where('id', '[0-9\.]+');

                $router -> get('destroy', 'ApiController@destroy');

                $router -> get('create', 'ApiController@createTable');

            });


        });

        if (app() -> runningInConsole() || app() -> environment('testing')) {
            return;
        }


        app('songshenzong') -> enable();
        app('songshenzong') -> boot();


    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['songshenzong', 'command.songshenzong.clear'];
    }


    /**
     * Publish the config file
     *
     * @param  string $configPath
     */
    protected function publishConfig($configPath)
    {
        $this -> publishes([$configPath => config_path('songshenzong.php')], 'config');
    }

}
