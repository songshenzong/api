# Songshenzong Api

A RESTful API package for the Laravel

## Installation

Require this package with composer:

```shell
composer require songshenzong/api
```

After updating composer, add the ServiceProvider to the providers array in `config/app.php`

### Laravel 5.x:

```php
Songshenzong\Api\ServiceProvider::class,
```


### Debug Information
If you not defined `SONGSHENZONG_API_DEBUG` in `.env`, it will use `APP_DEBUG`

```
SONGSHENZONG_API_DEBUG=true
```