# WREST (WordPress REST)
WREST - easy to use REST API wrapper for WordPress

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
