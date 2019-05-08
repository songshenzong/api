[![Build Status](https://travis-ci.org/songshenzong/api.svg?branch=master)][travis]
[![Total Downloads](https://poser.pugx.org/songshenzong/api/d/total.svg)][packagist]
[![Latest Stable Version](https://poser.pugx.org/songshenzong/api/v/stable.svg)][packagist]
[![License](https://poser.pugx.org/songshenzong/api/license.svg)][packagist]
[![PHP Version](https://img.shields.io/packagist/php-v/songshenzong/api.svg)][packagist]


## About

A RESTful API package for the Laravel

## Installation

Require this package with composer:

```shell
composer require songshenzong/api
```


## Laravel

Publish configuration files. If not, They can not be serialized correctly when you execute the `config:cache` Artisan command.

```shell
php artisan vendor:publish --provider="Songshenzong\Api\ServiceProvider"
```

## Documentation

Please refer to our extensive [Wiki documentation](https://github.com/songshenzong/api/wiki) for more information.


## Support

For answers you may not find in the Wiki, avoid posting issues. Feel free to ask for support on Songshenzong.com


## License

This package is licensed under the [BSD 3-Clause license](http://opensource.org/licenses/BSD-3-Clause).

[packagist]: https://packagist.org/packages/songshenzong/api
[travis]: https://travis-ci.org/songshenzong/api
