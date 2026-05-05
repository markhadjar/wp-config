# WP Config

Define WordPress constants using environment variables.

## Requirements

PHP 8.4+

## Installation

Install via Composer:

```bash
composer require markhadjar/wp-config
```

## Usage

Define constants with explicit values:

```php
$config = new \MarkHadjar\WpConfig\WpConfig();

$config
    ->set('DB_NAME', 'wordpress')
    ->set('DB_USER', 'root')
    ->set('DB_PASSWORD', 'password')
    ->set('DB_HOST', 'localhost');
```

Define constants from environment variables using typed defaults:

```php
$config = new \MarkHadjar\WpConfig\WpConfig();

$config
    ->env('DB_NAME', 'wordpress')
    ->env('DB_USER', 'root')
    ->env('DB_PASSWORD', 'password')
    ->env('DB_HOST', 'localhost');
```

Apply all entries as constants:

```php
$config->apply();
```

Check whether an entry exists or read its value:

```php
if ($config->has('DB_HOST')) {
    $host = $config->get('DB_HOST');
}
```

**Note:** Calling `set()` or `env()` with a key that is already a defined constant will throw a `ConstantWasAlreadyDefined` exception. Calling `apply()` will throw if a constant was defined externally with a different value between configuration and apply. Calling `env()` with a default value whose type is not `bool`, `int`, `float`, `string`, or `null` will throw a `DefaultValueTypeWasNotSupported` exception.
