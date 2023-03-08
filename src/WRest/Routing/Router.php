<?php

namespace Arafatkn\WRest\Routing;

use Arafatkn\WRest\Helpers\RouterMethodHelper;

class Router
{
	use RouterMethodHelper;

	private $namespace = '';

	private static $_instance;

	/**
	 * All the verbs supported by the router.
	 *
	 * @var string[]
	 */
	public static $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

	public $routes;

	protected $current;

	protected $groupStack = [];

	public function __construct()
	{
		$this->routes = new RouteCollection;
	}

	public static function resolveInstance()
	{
		if (!isset(self::$_instance) || is_null(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public static function __callStatic($name, $arguments)
	{
		return self::resolveInstance()->$name(...$arguments);
	}

	public function group($namespace, $callback)
	{
		$old_namespace = $this->namespace;
		$this->namespace .= $namespace;
		$callback($this);
		$this->namespace = $old_namespace;
	}

	/**
	 * Get the prefix from the last group on the stack.
	 *
	 * @return string
	 */
	public function getLastGroupPrefix()
	{
		if ($this->hasGroupStack()) {
			$last = end($this->groupStack);

			return isset($last['prefix']) ? $last['prefix'] : '';
		}

		return '';
	}

	/**
	 * Add a route to the underlying route collection.
	 *
	 * @param  array|string  $methods
	 * @param  string  $uri
	 * @param  array|string|callable|null  $action
	 * @return Route
	 */
	public function addRoute($methods, $uri, $action)
	{
		return $this->routes->add($this->createRoute($methods, $uri, $action));

//		if (is_string($action) && strstr($action, '@') !== false) {
//			$action = explode('@', $action, 2);
//		}
//
//		$methods = strtoupper(is_array($methods) ? implode(',', $methods) : $methods);
//
//		$this->routes[] = [
//			'methods'             => $methods,
//			'uri' => $uri,
//			'callback'            => $action,
//			'permission_callback' => [ $this, 'permission_check' ],
//			//'args'                => $args
//		];
//
//		return $this;
	}

	/**
	 * Create a new route instance.
	 *
	 * @param  array|string  $methods
	 * @param  string  $uri
	 * @param  mixed  $action
	 * @return Route
	 */
	protected function createRoute($methods, $uri, $action)
	{
		$route = $this->newRoute(
			$methods, $this->prefix($uri), $action
		);

		// If we have groups that need to be merged, we will merge them now after this
		// route has already been created and is ready to go. After we're done with
		// the merge we will be ready to return the route back out to the caller.
		//if ($this->hasGroupStack()) {
			//$this->mergeGroupAttributesIntoRoute($route);
		//}

		//$this->addWhereClausesToRoute($route);

		return $route;
	}

	/**
	 * Prefix the given URI with the last prefix.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	protected function prefix($uri)
	{
		return trim(trim($this->getLastGroupPrefix(), '/').'/'.trim($uri, '/'), '/') ?: '/';
	}

	/**
	 * Determine if the router currently has a group stack.
	 *
	 * @return bool
	 */
	public function hasGroupStack()
	{
		return ! empty($this->groupStack);
	}

	/**
	 * Get the current group stack for the router.
	 *
	 * @return array
	 */
	public function getGroupStack()
	{
		return $this->groupStack;
	}

	/**
	 * Prepend the last group namespace onto the use clause.
	 *
	 * @param  string  $class
	 * @return string
	 */
	protected function prependGroupNamespace($class)
	{
		$group = end($this->groupStack);

		return isset($group['namespace']) && strpos($class, '\\') !== 0
			? $group['namespace'].'\\'.$class : $class;
	}

	/**
	 * Prepend the last group controller onto the use clause.
	 *
	 * @param  string  $class
	 * @return string
	 */
	protected function prependGroupController($class)
	{
		$group = end($this->groupStack);

		if (! isset($group['controller'])) {
			return $class;
		}

		if (class_exists($class)) {
			return $class;
		}

		if (strpos($class, '@') !== false) {
			return $class;
		}

		return $group['controller'].'@'.$class;
	}

	/**
	 * Create a new Route object.
	 *
	 * @param  array|string  $methods
	 * @param  string  $uri
	 * @param  mixed  $action
	 * @return Route
	 */
	public function newRoute($methods, $uri, $action)
	{
		return (new Route($methods, $uri, $action))
			->setRouter($this);
	}

	public function registerAll()
	{
		foreach ($this->routes->getRoutesByMethod() as $method => $routes) {
			foreach ($routes as $route) {
				register_rest_route( '', $route->uri, [
					[
						'methods'             => $method,
						'callback'            => $route->action,
						//'permission_callback' => [ $this, 'permission_check' ],
						//'args'                => $args
					]
				] );
			}
		}
	}
}
