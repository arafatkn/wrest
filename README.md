# WREST (WordPress REST)
WREST - easy to use REST API wrapper for WordPress

## Installation

You can install wRest in two ways, via composer and manually.

### 1. Composer Installation

Add dependency in your project (theme/plugin):

```
composer require arafatkn/wrest
```

Now add `autoload.php` in your file if you haven't done already.

```php
require __DIR__ . '/vendor/autoload.php';
```

### 2. Manual Installation

Not Available Yet.

## Usage

```php
wrest()->get($uri, $callback);
wrest()->post($uri, $callback);
wrest()->put($uri, $callback);
wrest()->patch($uri, $callback);
wrest()->delete($uri, $callback);
wrest()->options($uri, $callback);
```


Passing a callback
```php
wrest()->get('greeting', function() {
    return 'Hello world';
});
```
