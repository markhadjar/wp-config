# WP Config

Define WordPress constants using environment variables.

## Requirements

PHP 8.4+

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

**Note:** Calling `set()` with a key that is already a defined constant will throw a `ConstantWasAlreadyDefined` exception. Calling `apply()` will throw if a constant was defined externally with a different value between configuration and apply.
