# Local web server for the development of Laravel

Feature:

- Provides a local HTTP/1 web server.
- Generates SSL certificates.
- A must-have tool when developing Laravel apps locally. *(If you don't have docker, homestead, etc... :smile:)*

## Installation

install package

```php
$ composer require sajya/server
```

## Usage

Use the command for run local web server:
```php
php artisan sajya:server
```

To regenerate a local certificate
```php
php artisan sajya:generate
```



## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.


