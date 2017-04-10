# Http Json

Http Json Response

## Installation

Require this package with composer:

```shell
composer require songshenzong/log
```

After updating composer, add the ServiceProvider to the providers array in `config/app.php`


### Laravel 5.x:

```php
Songshenzong\Log\ServiceProvider::class,
```

If you want to use the facade to log messages, add this to your facades in `config/app.php`:

```php
'Songshenzong' => Songshenzong\Log\Facade::class,
```

> If you use a `dingo/api` route, make sure you load the Songshenzong Middleware in `config/api.php`.
```php
    'middleware' => [
        'Songshenzong\Log\Middleware',
    ],
```





The profiler is enabled by default, You can override that in the config (`songshenzong.enabled`).



Copy the package config to your local config with the publish command:

```shell
php artisan vendor:publish --provider="Songshenzong\Log\ServiceProvider"
```
## Let's start
```
http://your.domain/songshenzong
```

## Usage

You can now add messages using the Facade (when added), using the PSR-3 levels (debug, info, notice, warning, error, critical, alert, emergency):

```php
Songshenzong::info($object);
Songshenzong::error('Error!');
Songshenzong::warning('Watch out…');
Songshenzong::addMessage('Another message', 'mylabel');
```

And start/stop timing:

```php
Songshenzong::startMeasure('render','Time for rendering');
Songshenzong::stopMeasure('render');
Songshenzong::addMeasure('now', LARAVEL_START, microtime(true));
Songshenzong::measure('My long operation', function() {
    // Do something…
});
```

Or log exceptions:

```php
try {
    throw new Exception('foobar');
} catch (Exception $e) {
    Songshenzong::addThrowable($e);
}
```

There are also helper functions available for the most common calls:

```php
// All arguments will be dumped as a debug message
songshenzong($var1, $someString, $intValue, $object);

start_measure('render','Time for rendering');
stop_measure('render');
add_measure('now', LARAVEL_START, microtime(true));
measure('My long operation', function() {
    // Do something…
});
```

If you want you can add your own DataCollectors, through the Container or the Facade:

```php
Songshenzong::addCollector(new Songshenzong\Log\DataCollector\MessagesCollector('my_messages'));
//Or via the App container:
$songshenzong = App::make('songshenzong');
$songshenzong->addCollector(new Songshenzong\Log\DataCollector\MessagesCollector('my_messages'));
```



Note: Not using the auto-inject, will disable the Request information, because that is added After the response.
You can add the default_request data collector in the config as alternative.

## Enabling/Disabling on run time
You can enable or disable the Songshenzong during run time.

```php
\Songshenzong::enable();
\Songshenzong::disable();
```

NB. Once enabled, the collectors are added (and could produce extra overhead), so if you want to use the Songshenzong in production, disable in the config and only enable when needed.
