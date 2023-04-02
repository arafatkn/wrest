![WREST](https://banners.beyondco.de/WREST.png?theme=light&packageManager=composer+require&packageName=arafatkn%2Fwrest&pattern=architect&style=style_1&description=Easy+to+use+fluent+REST+API+wrapper+for+WordPress.&md=1&showWatermark=0&fontSize=100px&images=code)

# WREST (WordPress REST)
WREST - easy to use fluent REST API wrapper for WordPress.

![Latest Stable Version](https://poser.pugx.org/arafatkn/wrest/v)
[![PHP Version Require](http://poser.pugx.org/arafatkn/wrest/require/php)](https://packagist.org/packages/arafatkn/wrest)
![License](https://poser.pugx.org/arafatkn/wrest/license)
[![Total Downloads](https://poser.pugx.org/arafatkn/wrest/downloads)](//packagist.org/packages/arafatkn/wrest)

## Installation

### Requirements
- PHP >= 5.6
- WordPress >= 4.4

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
WordPress API needs a namespace, so you have to set a namespace first.

One way is to set a default namespace before creating routes.
```php
wrest()->setNamespace('my-plugin/v1');

wrest()->get('hello', function() {
    return 'Hello world';
});
```

or you can set namespace for a group of routes.
```php
wrest()->usingNamespace('my-plugin/v1', function($wrest) {
    // You can use both $wrest or wrest() here
    $wrest->get('greeting', function(WP_REST_Request $req) {
        return 'Hello world';
    });
});
```

Passed callback will get a `WP_REST_Request` object as a parameter.

```php
wrest()->get('greeting', function(WP_REST_Request $req) {
    return 'Hello world';
});
```

#### More Examples

```php
wrest()->get('posts', $callback);
wrest()->post('posts', [$postController, 'store']);
wrest()->put($uri, $callback);
wrest()->patch($uri, $callback);
wrest()->delete($uri, $callback);
wrest()->any($uri, $callback); // All Routes GET, POST, PUT, PATCH, DELETE
wrest()->match(['GET', 'POST'], $uri, $callback);
```

### Permission Management

Passing a capability

```php
wrest()->get('greeting', function() {
    return 'Hello world';
})->permission('manage_options');
```

Passing a callback
```php
wrest()->get('greeting', function() {
    return 'Hello world';
})->permission(function(WP_REST_Request $req) {
    return is_user_logged_in();
});
```

### Parameters passing

```php
wrest()->get('/posts/{slug}', function(WP_REST_Request $request, $slug) {
    //
})->param('slug', '[A-Za-z]+');

wrest()->get('/user/{id}/{name}', function ($request, $id, $name) {
    //
})->param('id', '[0-9]+')->param('name', '[a-z]+');

wrest()->get('/user/{id}/{name}', function ($request, $id, $name) {
    //
})->param(['id' => '[0-9]+', 'name' => '[a-z]+']);
```

If you do not pass a regex for a param then `[^/]+` will be used as default.

```php
wrest()->get('/posts/{slug}', function(WP_REST_Request $request, $slug) {
    // Also Works. slug will contain all the characters between posts/ and next /.
});
```

### Route Action

Action can be a callback, a class method, a static class method or a non-static class method and can be passed as below.

```php
wrest()->get('posts', function() => {});
wrest()->get('posts', 'getAllPosts'); // getAllPosts is a function.
wrest()->get('posts', 'PostController@getAll'); // getAll is static function.
wrest()->get('pages', [$pageController, 'getAll']); // getAll is non-static function.
wrest()->get('authors', [CommentController::class, 'getAll']); // getAll is static function.
```
All the functions will get a `WP_REST_Request` object as a parameter.

## Supported Features
- [x] Namespaces for All Routes.
- [x] Normal Routes.
- [x] Routes with Parameters.
- [x] Route Actions.
- [x] Permission Management.

#### Upcoming Features
- [ ] Add support for route groups.
- [ ] Add support for namespace on the fly.
- [ ] Add support for namespace groups.
- [ ] Add support for resource routes.
- [ ] Add support for schema.
- [ ] Add support route redirection.
- [ ] Add support for passing matched parameters directly to actions.

## Credits
Special thanks to [Tareq Hasan](https://gist.github.com/tareq1988/be49697425326fc90952de835313ff6b) for this awesome idea.

## Contribution Guide

This is still in beta, though I have a confidence that it will work as expected.
You can contribute by reporting bugs, fixing bugs, reviewing pull requests and more ways.
Go to [**issues**](https://github.com/arafatkn/wrest/issues) section, and you can start working on a issue immediately.
If you want to add or fix something, open a pull request.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.