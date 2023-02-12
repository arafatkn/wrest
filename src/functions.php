<?php

/**
 * Functions
 */

if (!function_exists('wrest')) {
	function wrest() {
		return \Arafatkn\WRest\Routing\Router::resolveInstance();
	}
}

add_action( 'rest_api_init', function () {
	wrest()->registerAll();
});

if (! function_exists('value')) {
	/**
	 * Return the default value of the given value.
	 *
	 * @param  mixed  $value
	 * @return mixed
	 */
	function value($value, ...$args)
	{
		return $value instanceof Closure ? $value(...$args) : $value;
	}
}
