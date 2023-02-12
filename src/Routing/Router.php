<?php

namespace Arafatkn\WRest\Routing;

use Closure;

class Router
{
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

	/**
	 * Get the prefix from the last group on the stack.
	 *
	 * @return string
	 */
	public function getLastGroupPrefix()
	{
		if ($this->hasGroupStack()) {
			$last = end($this->groupStack);

			return $last['prefix'] ?? '';
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

		if (is_string($action) && strstr($action, '@') !== false) {
			$action = explode('@', $action, 2);
		}

		$methods = strtoupper(is_array($methods) ? implode(',', $methods) : $methods);

		$this->routes[] = [
			'methods'             => $methods,
			'uri' => $uri,
			'callback'            => $action,
			'permission_callback' => [ $this, 'permission_check' ],
			//'args'                => $args
		];

		return $this;
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
		// If the route is routing to a controller we will parse the route action into
		// an acceptable array format before registering it and creating this route
		// instance itself. We need to build the Closure that will call this out.
		if ($this->actionReferencesController($action)) {
			$action = $this->convertToControllerAction($action);
		}

		$route = $this->newRoute(
			$methods, $this->prefix($uri), $action
		);

		// If we have groups that need to be merged, we will merge them now after this
		// route has already been created and is ready to go. After we're done with
		// the merge we will be ready to return the route back out to the caller.
		if ($this->hasGroupStack()) {
			//$this->mergeGroupAttributesIntoRoute($route);
		}

		//$this->addWhereClausesToRoute($route);

		return $route;
	}

	/**
	 * Determine if the action is routing to a controller.
	 *
	 * @param  mixed  $action
	 * @return bool
	 */
	protected function actionReferencesController($action)
	{
		if (! $action instanceof Closure) {
			return is_string($action) || (isset($action['uses']) && is_string($action['uses']));
		}

		return false;
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
	 * Add a controller based route action to the action array.
	 *
	 * @param  array|string  $action
	 * @return array
	 */
	protected function convertToControllerAction($action)
	{
		if (is_string($action)) {
			$action = ['uses' => $action];
		}

		// Here we'll merge any group "controller" and "uses" statements if necessary so that
		// the action has the proper clause for this property. Then, we can simply set the
		// name of this controller on the action plus return the action array for usage.
		if ($this->hasGroupStack()) {
			$action['uses'] = $this->prependGroupController($action['uses']);
			$action['uses'] = $this->prependGroupNamespace($action['uses']);
		}

		// Here we will set this controller name on the action array just so we always
		// have a copy of it for reference if we need it. This can be used while we
		// search for a controller name or do some other type of fetch operation.
		$action['controller'] = $action['uses'];

		return $action;
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
		foreach ($this->routes as $route) {
//			register_rest_route( '', $uri, [
//				[
//					'methods'             => $methods,
//					'callback'            => $action,
//					'permission_callback' => [ $this, 'permission_check' ],
//					//'args'                => $args
//				]
//			] );
		}
	}
}
