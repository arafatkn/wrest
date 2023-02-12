<?php

namespace Arafatkn\WRest\Helpers;

use Arafatkn\WRest\Routing\Route;

trait RouterMethodHelper
{
	/**
	 * Register a new GET route with the router.
	 *
	 * @param  string  $uri
	 * @param  array|string|callable|null  $action
	 * @return Route
	 */
	public function get($uri, $action = null)
	{
		return $this->addRoute(['GET', 'HEAD'], $uri, $action);
	}

	/**
	 * Register a new POST route with the router.
	 *
	 * @param  string  $uri
	 * @param  array|string|callable|null  $action
	 * @return Route
	 */
	public function post($uri, $action = null)
	{
		return $this->addRoute('POST', $uri, $action);
	}

	/**
	 * Register a new PUT route with the router.
	 *
	 * @param  string  $uri
	 * @param  array|string|callable|null  $action
	 * @return Route
	 */
	public function put($uri, $action = null)
	{
		return $this->addRoute('PUT', $uri, $action);
	}

	/**
	 * Register a new PATCH route with the router.
	 *
	 * @param  string  $uri
	 * @param  array|string|callable|null  $action
	 * @return Route
	 */
	public function patch($uri, $action = null)
	{
		return $this->addRoute('PATCH', $uri, $action);
	}

	/**
	 * Register a new DELETE route with the router.
	 *
	 * @param  string  $uri
	 * @param  array|string|callable|null  $action
	 * @return Route
	 */
	public function delete($uri, $action = null)
	{
		return $this->addRoute('DELETE', $uri, $action);
	}

	/**
	 * Register a new OPTIONS route with the router.
	 *
	 * @param  string  $uri
	 * @param  array|string|callable|null  $action
	 * @return Route
	 */
	public function options($uri, $action = null)
	{
		return $this->addRoute('OPTIONS', $uri, $action);
	}

	/**
	 * Register a new route responding to all verbs.
	 *
	 * @param  string  $uri
	 * @param  array|string|callable|null  $action
	 * @return Route
	 */
	public function any($uri, $action = null)
	{
		return $this->addRoute(self::$verbs, $uri, $action);
	}

	/**
	 * Register a new route with the given verbs.
	 *
	 * @param  array|string  $methods
	 * @param  string  $uri
	 * @param  array|string|callable|null  $action
	 * @return Route
	 */
	public function match($methods, $uri, $action = null)
	{
		return $this->addRoute($methods, $uri, $action);
	}
}