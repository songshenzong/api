<?php namespace Songshenzong\Log;

use Songshenzong\Log\DataCollector\AuthCollector;
use Songshenzong\Log\DataCollector\EventCollector;
use Songshenzong\Log\DataCollector\FilesCollector;
use Songshenzong\Log\DataCollector\GateCollector;
use Songshenzong\Log\DataCollector\LaravelCollector;
use Songshenzong\Log\DataCollector\LogsCollector;
use Songshenzong\Log\DataCollector\MultiAuthCollector;
use Songshenzong\Log\DataCollector\QueryCollector;
use Songshenzong\Log\DataCollector\SessionCollector;
use Songshenzong\Log\DataCollector\ViewCollector;
use Songshenzong\Log\DataCollector\RequestCollector;

use Songshenzong\Log\DataCollector\ConfigCollector;
use Songshenzong\Log\DataCollector\ExceptionsCollector;
use Songshenzong\Log\DataCollector\MemoryCollector;
use Songshenzong\Log\DataCollector\MessagesCollector;
use Songshenzong\Log\DataCollector\PhpInfoCollector;
use Songshenzong\Log\DataCollector\TimeDataCollector;

use Songshenzong\Log\Storage\FilesystemStorage;
use Songshenzong\Log\Bridge\MonologCollector;
use Songshenzong\Log\Bridge\SwiftMailer\SwiftLogCollector;
use Songshenzong\Log\Bridge\SwiftMailer\SwiftMailCollector;


use Songshenzong\Log\DataFormatter\QueryFormatter;
use Songshenzong\Log\DebugBar;
use Songshenzong\Log\Storage\PdoStorage;
use Songshenzong\Log\Storage\RedisStorage;
use Exception;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Session\SessionManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Debug bar subclass which adds all without Request and with LaravelCollector.
 * Rest is added in Service Provider
 *
 * @method void emergency($message)
 * @method void alert($message)
 * @method void critical($message)
 * @method void error($message)
 * @method void warning($message)
 * @method void notice($message)
 * @method void info($message)
 * @method void debug($message)
 * @method void log($message)
 */
class LaravelDebugbar extends DebugBar
{
    /**
     * The Laravel application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Normalized Laravel Version
     *
     * @var string
     */
    protected $version;

    /**
     * True when booted.
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * True when enabled, false disabled an null for still unknown
     *
     * @var bool
     */
    protected $enabled = null;

    /**
     * True when this is a Lumen application
     *
     * @var bool
     */
    protected $is_lumen = false;


    /**
     * @param Application $app
     */
    public function __construct($app = null)
    {
        if (!$app) {
            $app = app();   //Fallback when $app is not given
        }
        $this -> app      = $app;
        $this -> version  = $app -> version();
        $this -> is_lumen = str_contains($this -> version, 'Lumen');
    }

    /**
     * Enable and boot, if not already booted.
     */
    public function enable()
    {
        $this -> enabled = true;

        if (!$this -> booted) {
            $this -> boot();
        }
    }

    /**
     * Boot  (add collectors, renderer and listener)
     */
    public function boot()
    {
        if ($this -> booted) {
            return;
        }

        /** @var \Songshenzong\Log\LaravelDebugbar $debugbar */
        $debugbar = $this;

        /** @var Application $app */
        $app = $this -> app;

        // Set custom error handler
        set_error_handler([$this, 'handleError']);

        /**---------------------------------------------------------
         *   phpinfo
         *---------------------------------------------------------*/
        if ($this -> shouldCollect('phpinfo', true)) {
            $this -> addCollector(new PhpInfoCollector());
        }

        /**---------------------------------------------------------
         *   Messages
         *---------------------------------------------------------*/
        if ($this -> shouldCollect('messages', true)) {
            $this -> addCollector(new MessagesCollector());
        }


        /**---------------------------------------------------------
         *   time
         *---------------------------------------------------------*/
        if ($this -> shouldCollect('time', true)) {
            $this -> addCollector(new TimeDataCollector());

            if (!$this -> isLumen()) {
                $this -> app -> booted(
                    function () use ($debugbar) {
                        $startTime = $this -> app['request'] -> server('REQUEST_TIME_FLOAT');
                        if ($startTime) {
                            $debugbar['time'] -> addMeasure('Booting', $startTime, microtime(true));
                        }
                    }
                );
            }

            $debugbar -> startMeasure('application', 'Application');
        }


        /**---------------------------------------------------------
         *   memory
         *---------------------------------------------------------*/
        if ($this -> shouldCollect('memory', true)) {
            $this -> addCollector(new MemoryCollector());
        }

        /**---------------------------------------------------------
         *   exceptions
         *---------------------------------------------------------*/
        if ($this -> shouldCollect('exceptions', true)) {
            try {
                $exceptionCollector = new ExceptionsCollector();
                $exceptionCollector -> setChainExceptions(
                    $this -> app['config'] -> get('songshenzong.options.exceptions.chain', true)
                );
                $this -> addCollector($exceptionCollector);
            } catch (\Exception $e) {
            }
        }

        /**---------------------------------------------------------
         *   laravel
         *---------------------------------------------------------*/
        if ($this -> shouldCollect('laravel', false)) {
            $this -> addCollector(new LaravelCollector($this -> app));
        }


        /**---------------------------------------------------------
         *   All events fired
         *---------------------------------------------------------*/
        if ($this -> shouldCollect('events', false) && isset($this -> app['events'])) {
            try {
                $startTime      = $this -> app['request'] -> server('REQUEST_TIME_FLOAT');
                $eventCollector = new EventCollector($startTime);
                $this -> addCollector($eventCollector);
                $this -> app['events'] -> subscribe($eventCollector);

            } catch (\Exception $e) {
                $this -> addThrowable(
                    new Exception(
                        'Cannot add EventCollector to Songshenzong: ' . $e -> getMessage(),
                        $e -> getCode(),
                        $e
                    )
                );
            }
        }

        /**---------------------------------------------------------
         *   Views with their data
         *---------------------------------------------------------*/
        if ($this -> shouldCollect('views', true) && isset($this -> app['events'])) {
            try {
                $collectData = $this -> app['config'] -> get('songshenzong.options.views.data', true);
                $this -> addCollector(new ViewCollector($collectData));
                $this -> app['events'] -> listen(
                    'composing:*',
                    function ($view, $data = []) use ($debugbar) {
                        if ($data) {
                            $view = $data[0]; // For Laravel >= 5.4
                        }
                        $debugbar['views'] -> addView($view);
                    }
                );
            } catch (\Exception $e) {
                $this -> addThrowable(
                    new Exception(
                        'Cannot add ViewCollector to Songshenzong: ' . $e -> getMessage(), $e -> getCode(), $e
                    )
                );
            }
        }


        /**---------------------------------------------------------
         *   Current route information
         *---------------------------------------------------------*/
        if (!$this -> isLumen() && $this -> shouldCollect('route')) {
            try {
                $this -> addCollector($this -> app -> make('Songshenzong\Log\DataCollector\IlluminateRouteCollector'));
            } catch (\Exception $e) {
                $this -> addThrowable(
                    new Exception(
                        'Cannot add RouteCollector to Songshenzong: ' . $e -> getMessage(),
                        $e -> getCode(),
                        $e
                    )
                );
            }
        }


        /**---------------------------------------------------------
         *   Logs from Mongolog (merged in messages if enabled)
         *---------------------------------------------------------*/
        if (!$this -> isLumen() && $this -> shouldCollect('log', true)) {

            try {
                if ($this -> hasCollector('messages')) {
                    $logger = new MessagesCollector('log');
                    $this['messages'] -> aggregate($logger);
                    $this -> app['log'] -> listen(
                        function ($level, $message = null, $context = null) use ($logger) {
                            // Laravel 5.4 changed how the global log listeners are called. We must account for
                            // the first argument being an "event object", where arguments are passed
                            // via object properties, instead of individual arguments.
                            if ($level instanceof \Illuminate\Log\Events\MessageLogged) {
                                $message = $level -> message;
                                $context = $level -> context;
                                $level   = $level -> level;
                            }

                            try {
                                $logMessage = (string)$message;
                                if (mb_check_encoding($logMessage, 'UTF-8')) {
                                    $logMessage .= (!empty($context) ? ' ' . json_encode($context) : '');
                                } else {
                                    $logMessage = "[INVALID UTF-8 DATA]";
                                }
                            } catch (\Exception $e) {
                                $logMessage = "[Exception: " . $e -> getMessage() . "]";
                            }
                            $logger -> addMessage(
                                '[' . date('H:i:s') . '] ' . "LOG.$level: " . $logMessage,
                                $level,
                                false
                            );
                        }
                    );
                } else {
                    $this -> addCollector(new MonologCollector($this -> app['log'] -> getMonolog()));
                }
            } catch (\Exception $e) {
                $this -> addThrowable(
                    new Exception(
                        'Cannot add LogsCollector to Songshenzong: ' . $e -> getMessage(), $e -> getCode(), $e
                    )
                );
            }

        }


        /**---------------------------------------------------------
         *   Show database (PDO) queries and bindings
         *---------------------------------------------------------*/
        if ($this -> shouldCollect('db', true) && isset($this -> app['db'])) {
            $db = $this -> app['db'];
            if ($debugbar -> hasCollector('time') && $this -> app['config'] -> get(
                    'songshenzong.options.db.timeline',
                    false
                )
            ) {
                $timeCollector = $debugbar -> getCollector('time');
            } else {
                $timeCollector = null;
            }
            $queryCollector = new QueryCollector($timeCollector);

            $queryCollector -> setDataFormatter(new QueryFormatter());

            if ($this -> app['config'] -> get('songshenzong.options.db.with_params')) {
                $queryCollector -> setRenderSqlWithParams(true);
            }

            if ($this -> app['config'] -> get('songshenzong.options.db.backtrace')) {
                $middleware = !$this -> is_lumen ? $this -> app['router'] -> getMiddleware() : [];
                $queryCollector -> setFindSource(true, $middleware);
            }

            if ($this -> app['config'] -> get('songshenzong.options.db.explain.enabled')) {
                $types = $this -> app['config'] -> get('songshenzong.options.db.explain.types');
                $queryCollector -> setExplainSource(true, $types);
            }

            if ($this -> app['config'] -> get('songshenzong.options.db.hints', true)) {
                $queryCollector -> setShowHints(true);
            }

            $this -> addCollector($queryCollector);

            try {
                $db -> listen(
                    function ($query, $bindings = null, $time = null, $connectionName = null) use ($db, $queryCollector) {
                        // Laravel 5.2 changed the way some core events worked. We must account for
                        // the first argument being an "event object", where arguments are passed
                        // via object properties, instead of individual arguments.
                        if ($query instanceof \Illuminate\Database\Events\QueryExecuted) {
                            $bindings   = $query -> bindings;
                            $time       = $query -> time;
                            $connection = $query -> connection;

                            $query = $query -> sql;
                        } else {
                            $connection = $db -> connection($connectionName);
                        }

                        $queryCollector -> addQuery((string)$query, $bindings, $time, $connection);
                    }
                );
            } catch (\Exception $e) {
                $this -> addThrowable(
                    new Exception(
                        'Cannot add listen to Queries for Songshenzong: ' . $e -> getMessage(),
                        $e -> getCode(),
                        $e
                    )
                );
            }

            try {
                $db -> getEventDispatcher() -> listen([
                                                          \Illuminate\Database\Events\TransactionBeginning::class,
                                                          'connection.*.beganTransaction',
                                                      ], function ($transaction) use ($queryCollector) {
                    $queryCollector -> collectTransactionEvent('Begin Transaction', $transaction -> connection);
                });

                $db -> getEventDispatcher() -> listen([
                                                          \Illuminate\Database\Events\TransactionCommitted::class,
                                                          'connection.*.committed',
                                                      ], function ($transaction) use ($queryCollector) {
                    $queryCollector -> collectTransactionEvent('Commit Transaction', $transaction -> connection);
                });

                $db -> getEventDispatcher() -> listen([
                                                          \Illuminate\Database\Events\TransactionRolledBack::class,
                                                          'connection.*.rollingBack',
                                                      ], function ($transaction) use ($queryCollector) {
                    $queryCollector -> collectTransactionEvent('Rollback Transaction', $transaction -> connection);
                });
            } catch (\Exception $e) {
                $this -> addThrowable(
                    new Exception(
                        'Cannot add listen transactions to Queries for Songshenzong: ' . $e -> getMessage(),
                        $e -> getCode(),
                        $e
                    )
                );
            }
        }


        /**---------------------------------------------------------
         *   Catch mail messages
         *---------------------------------------------------------*/
        if ($this -> shouldCollect('mail', true) && class_exists('Illuminate\Mail\MailServiceProvider')) {
            try {
                $mailer = $this -> app['mailer'] -> getSwiftMailer();
                $this -> addCollector(new SwiftMailCollector($mailer));
                if ($this -> app['config'] -> get('songshenzong.options.mail.full_log') && $this -> hasCollector(
                        'messages'
                    )
                ) {
                    $this['messages'] -> aggregate(new SwiftLogCollector($mailer));
                }
            } catch (\Exception $e) {
                $this -> addThrowable(
                    new Exception(
                        'Cannot add MailCollector to Songshenzong: ' . $e -> getMessage(), $e -> getCode(), $e
                    )
                );
            }
        }


        /**---------------------------------------------------------
         *   Add the latest log messages
         *---------------------------------------------------------*/
        if ($this -> shouldCollect('logs', false)) {
            try {
                $file = $this -> app['config'] -> get('songshenzong.options.logs.file');
                $this -> addCollector(new LogsCollector($file));
            } catch (\Exception $e) {
                $this -> addThrowable(
                    new Exception(
                        'Cannot add LogsCollector to Songshenzong: ' . $e -> getMessage(), $e -> getCode(), $e
                    )
                );
            }
        }

        /**---------------------------------------------------------
         *   Show the included files
         *---------------------------------------------------------*/
        if ($this -> shouldCollect('files', false)) {
            $this -> addCollector(new FilesCollector($app));
        }


        /**---------------------------------------------------------
         *   Display Laravel authentication status
         *---------------------------------------------------------*/
        if ($this -> shouldCollect('auth', false)) {
            try {
                if ($this -> checkVersion('5.2')) {
                    // fix for compatibility with Laravel 5.2.*
                    $guards        = array_keys($this -> app['config'] -> get('auth.guards'));
                    $authCollector = new MultiAuthCollector($app['auth'], $guards);
                } else {
                    $authCollector = new AuthCollector($app['auth']);
                }

                $authCollector -> setShowName(
                    $this -> app['config'] -> get('songshenzong.options.auth.show_name')
                );
                $this -> addCollector($authCollector);
            } catch (\Exception $e) {
                $this -> addThrowable(
                    new Exception(
                        'Cannot add AuthCollector to Songshenzong: ' . $e -> getMessage(), $e -> getCode(), $e
                    )
                );
            }
        }

        /**---------------------------------------------------------
         *   Display Laravel Gate checks
         *---------------------------------------------------------*/
        if ($this -> shouldCollect('gate', false)) {
            try {
                $gateCollector = $this -> app -> make('Songshenzong\Log\DataCollector\GateCollector');
                $this -> addCollector($gateCollector);
            } catch (\Exception $e) {
                // No Gate collector
            }
        }


        $this -> booted = true;
    }

    public function shouldCollect($name, $default = false)
    {
        return config('songshenzong.collectors.' . $name, $default);
    }

    /**
     * Handle silenced errors
     *
     * @param        $level
     * @param        $message
     * @param string $file
     * @param int    $line
     * @param array  $context
     *
     * @throws \ErrorException
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if (error_reporting() & $level) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        } else {
            $this -> addMessage($message, 'deprecation');
        }
    }

    /**
     * Starts a measure
     *
     * @param string $name  Internal name, used to stop the measure
     * @param string $label Public name
     */
    public function startMeasure($name, $label = null)
    {
        if ($this -> hasCollector('time')) {
            /** @var \Songshenzong\Log\DataCollector\TimeDataCollector $collector */
            $collector = $this -> getCollector('time');
            $collector -> startMeasure($name, $label);
        }
    }

    /**
     * Stops a measure
     *
     * @param string $name
     */
    public function stopMeasure($name)
    {
        if ($this -> hasCollector('time')) {
            /** @var \Songshenzong\Log\DataCollector\TimeDataCollector $collector */
            $collector = $this -> getCollector('time');
            try {
                $collector -> stopMeasure($name);
            } catch (\Exception $e) {
                //  $this->addThrowable($e);
            }
        }
    }

    /**
     * Adds an exception to be profiled in the debug bar
     *
     * @param Exception $e
     *
     * @deprecated in favor of addThrowable
     */
    public function addException(Exception $e)
    {
        return $this -> addThrowable($e);
    }

    /**
     * Adds an exception to be profiled in the debug bar
     *
     * @param Exception $e
     */
    public function addThrowable($e)
    {
        if ($this -> hasCollector('exceptions')) {
            /** @var \Songshenzong\Log\DataCollector\ExceptionsCollector $collector */
            $collector = $this -> getCollector('exceptions');
            $collector -> addThrowable($e);
        }
    }


    /**
     * Modify the response and inject  (or data in headers)
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function modifyResponse(Request $request, Response $response)
    {


        $app = $this -> app;


        if ($app -> runningInConsole() || !$this -> isEnabled() || $this -> isDebugbarRequest()) {
            return $response;
        }

        // Show the Http Response Exception, when available
        if (isset($response -> exception)) {
            $this -> addThrowable($response -> exception);
        }

        /**---------------------------------------------------------
         *   Display config settings
         *---------------------------------------------------------*/

        if ($this -> shouldCollect('config', false)) {
            try {
                $configCollector = new ConfigCollector();
                $configCollector -> setData($app['config'] -> all());
                $this -> addCollector($configCollector);
            } catch (\Exception $e) {
                $this -> addThrowable(
                    new Exception(
                        'Cannot add ConfigCollector to Songshenzong: ' . $e -> getMessage(),
                        $e -> getCode(),
                        $e
                    )
                );
            }
        }


        if ($this -> app -> bound(SessionManager::class)) {

            /** @var \Illuminate\Session\SessionManager $sessionManager */
            $sessionManager = $app -> make(SessionManager::class);
            $httpDriver     = new SymfonyHttpDriver($sessionManager, $response);
            $this -> setHttpDriver($httpDriver);

            if ($this -> shouldCollect('session') && !$this -> hasCollector('session')) {
                try {
                    $this -> addCollector(new SessionCollector($sessionManager));
                } catch (\Exception $e) {
                    $this -> addThrowable(
                        new Exception(
                            'Cannot add SessionCollector to Songshenzong: ' . $e -> getMessage(),
                            $e -> getCode(),
                            $e
                        )
                    );
                }
            }
        } else {
            $sessionManager = null;
        }

        /**---------------------------------------------------------
         *   Only one can be enabled.
         *---------------------------------------------------------*/
        if ($this -> shouldCollect('request', true) && !$this -> hasCollector('request')) {
            try {
                $this -> addCollector(new RequestCollector($request, $response, $sessionManager));
            } catch (\Exception $e) {
                $this -> addThrowable(
                    new Exception(
                        'Cannot add RequestCollector to Songshenzong: ' . $e -> getMessage(),
                        $e -> getCode(),
                        $e
                    )
                );
            }
        }


        /**---------------------------------------------------------
         *   Just collect + store data
         *---------------------------------------------------------*/
        try {
            $this -> collect();
        } catch (\Exception $e) {
            $app['log'] -> error('Songshenzong exception: ' . $e -> getMessage());
        }


        return $response;
    }

    /**
     * Check if is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {


        if ($this -> enabled === null) {
            $this -> enabled = value($this -> app['config'] -> get('songshenzong.enabled'));
        }

        if ($this -> enabled === true) {
            $environments = config('songshenzong.env', ['dev', 'local', 'production']);

            $this -> enabled = in_array(env('APP_ENV'), $environments);
        }

        return $this -> enabled;
    }

    /**
     * Check if this is a request to the self.
     *
     * @return bool
     */
    protected function isDebugbarRequest()
    {
        return $this -> app['request'] -> segment(1) == 'songshenzong';
    }


    /**
     * Collects the data from the collectors
     *
     * @return array
     */
    public function collect()
    {
        /** @var Request $request */
        $request = app('request');

        $this -> data = [
            '__meta' => [
                'time'   => microtime(true),
                'method' => $request -> getMethod(),
                'uri'    => $request -> getRequestUri(),
                'ip'     => $request -> getClientIp(),
            ],
        ];

        foreach ($this -> collectors as $name => $collector) {
            $this -> data[$name] = $collector -> collect();
        }

        // Remove all invalid (non UTF-8) characters
        array_walk_recursive(
            $this -> data,
            function (&$item) {
                if (is_string($item) && !mb_check_encoding($item, 'UTF-8')) {
                    $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
                }
            }
        );


        $this -> persistData();


        return $this -> data;
    }

    // Persist the collect information the database
    private function persistData()
    {
        $meta = $this -> data['__meta'];
        $data = [
            'data'   => $this -> data,
            'time'   => $meta['time'],
            'uri'    => $meta['uri'],
            'ip'     => $meta['ip'],
            'method' => $meta['method'],
        ];

        return SongshenzongLog ::create($data);
    }


    /**
     * Disable
     */
    public function disable()
    {
        $this -> enabled = false;
    }

    /**
     * Adds a measure
     *
     * @param string $label
     * @param float  $start
     * @param float  $end
     */
    public function addMeasure($label, $start, $end)
    {
        if ($this -> hasCollector('time')) {
            /** @var \Songshenzong\Log\DataCollector\TimeDataCollector $collector */
            $collector = $this -> getCollector('time');
            $collector -> addMeasure($label, $start, $end);
        }
    }

    /**
     * Utility function to measure the execution of a Closure
     *
     * @param string   $label
     * @param \Closure $closure
     */
    public function measure($label, \Closure $closure)
    {
        if ($this -> hasCollector('time')) {
            /** @var \Songshenzong\Log\DataCollector\TimeDataCollector $collector */
            $collector = $this -> getCollector('time');
            $collector -> measure($label, $closure);
        } else {
            $closure();
        }
    }

    /**
     * Collect data in a CLI request
     *
     * @return array
     */
    public function collectConsole()
    {
        if (!$this -> isEnabled()) {
            return;
        }


        $this -> data = [
            '__meta' => [
                'time'   => microtime(true),
                'method' => 'CLI',
                'uri'    => isset($_SERVER['argv']) ? implode(' ', $_SERVER['argv']) : null,
                'ip'     => isset($_SERVER['SSH_CLIENT']) ? $_SERVER['SSH_CLIENT'] : null,
            ],
        ];

        foreach ($this -> collectors as $name => $collector) {
            $this -> data[$name] = $collector -> collect();
        }

        // Remove all invalid (non UTF-8) characters
        array_walk_recursive(
            $this -> data,
            function (&$item) {
                if (is_string($item) && !mb_check_encoding($item, 'UTF-8')) {
                    $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
                }
            }
        );


        $this -> persistData();


        return $this -> data;
    }

    /**
     * Magic calls for adding messages
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed|void
     */
    public function __call($method, $args)
    {
        $messageLevels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug', 'log'];
        if (in_array($method, $messageLevels)) {
            foreach ($args as $arg) {
                $this -> addMessage($arg, $method);
            }
        }
    }

    /**
     * Adds a message to the MessagesCollector
     *
     * A message can be anything from an object to a string
     *
     * @param mixed  $message
     * @param string $label
     */
    public function addMessage($message, $label = 'info')
    {
        if ($this -> hasCollector('messages')) {
            /** @var \Songshenzong\Log\DataCollector\MessagesCollector $collector */
            $collector = $this -> getCollector('messages');
            $collector -> addMessage($message, $label);
        }
    }

    /**
     * Check the version of Laravel
     *
     * @param string $version
     * @param string $operator (default: '>=')
     *
     * @return boolean
     */
    protected function checkVersion($version, $operator = ">=")
    {
        return version_compare($this -> version, $version, $operator);
    }

    protected function isLumen()
    {
        return $this -> is_lumen;
    }

    /**
     * Basic Json method.
     *
     * @param      $status_code
     * @param      $message
     * @param null $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function json($status_code, $message, $data = null)
    {
        return response() -> json([
                                      'status_code' => $status_code,
                                      'message'     => $message,
                                      'data'        => $data,
                                      'token'       => \request() -> token,
                                  ]);
    }


}
