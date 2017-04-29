# ResponseJson

Http Json Response

## Installation

Require this package with composer:

```shell
composer require songshenzong/response-json
```

After updating composer, add the ServiceProvider to the providers array in `config/app.php`

### Laravel 5.x:

```php
Songshenzong\ResponseJson\ServiceProvider::class,
```


### Debug Information
If you not defined `RESPONSE_JSON_DEBUG` in `.env`, it will use `APP_DEBUG`

```
RESPONSE_JSON_DEBUG=true
```