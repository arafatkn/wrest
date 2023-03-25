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
	public static $verbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

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

	public function setNamespace($namespace)
	{
		$this->namespace = $namespace;
	}

	public function usingNamespace($namespace, $callback)
	{
		$old_namespace = $this->namespace;
		$this->namespace = $namespace;
		$callback($this);
		$this->namespace = $old_namespace;
	}

	public function group($group, $callback)
	{
		$old = $this->groupStack;
		$this->groupStack[] = $group;
		$callback($this);
		$this->groupStack = $old;
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
		// Support for the "Controller@method" syntax.
		// Method have to be static.
		if (is_string($action) && strstr($action, '@') !== false) {
			$action = explode('@', $action, 2);
		}

		return $this->routes->add($this->createRoute($methods, $this->namespace, $uri, $action));
	}

	/**
	 * Create a new route instance.
	 *
	 * @param  array|string  $methods
	 * @param  string  $namespace
	 * @param  string  $uri
	 * @param  mixed  $action
	 * @return Route
	 */
	protected function createRoute($methods, $namespace, $uri, $action)
	{
		$route = (new Route($methods, $namespace, $uri, $action))
			->setRouter($this);

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

	public function registerAll()
	{
		foreach ($this->routes->getRoutes() as $route) {
			//echo json_encode($route) . '<br/>';

			register_rest_route($route->namespace, $route->uri, [
				[
					'methods'             => $route->methods,
					'callback'            => $route->action,
					//'permission_callback' => [ $this, 'permission_check' ],
					//'args'                => $args
				]
			]);
		}
	}
}
