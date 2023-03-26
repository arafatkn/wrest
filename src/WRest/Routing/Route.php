<?php

namespace Arafatkn\WRest\Routing;

class Route
{
	public $methods;

	public $namespace = null;

	protected $domain = null;

	public $uri;

	public $action;

	public $permission;

	public $params = [];

	public $args = [];

	protected $router;

	public function __construct($methods, $namespace, $uri, $action)
	{
		$this->methods = (array) $methods;
		$this->namespace = $namespace;
		$this->uri = $uri;
		$this->action = $action;
	}

	/**
	 * Get the domain defined for the route.
	 *
	 * @return string|null
	 */
	public function getDomain()
	{
		return $this->domain;
	}

	/**
	 * Get the URI associated with the route.
	 *
	 * @return string
	 */
	public function uri()
	{
		return $this->uri;
	}

	/**
	 * Get the HTTP verbs the route responds to.
	 *
	 * @return array
	 */
	public function methods()
	{
		return $this->methods;
	}

	/**
	 * Set the router instance on the route.
	 *
	 * @param  Router  $router
	 * @return $this
	 */
	public function setRouter(Router $router)
	{
		$this->router = $router;

		return $this;
	}

	/**
	 * Set Permission checker for this route.
	 *
	 * @param string|callable $checker
	 * @return $this
	 */
	public function permission($checker)
	{
		if (is_string($checker)) {
			$this->permission = function () use ($checker) {
				return current_user_can($checker);
			};
		} else {
			$this->permission = $checker;
		}

		return $this;
	}

	/**
	 * Set a regular expression requirement on the route.
	 *
	 * @param  array|string  $name
	 * @param  string|null  $expression
	 * @return $this
	 */
	public function param($name, $expression = null)
	{
		$params = is_array($name) ? $name : [$name => $expression];

		foreach ($params as $name => $expression) {
			$this->params[$name] = $expression;
		}

		return $this;
	}

	/**
	 * Set args
	 *
	 * @param array $args
	 */
	public function args($args) {
		foreach ($args as $name => $rules) {
			$this->args[$name] = $rules;
		}

		return $this;
	}

	/**
	 * Set argument on the route.
	 *
	 * @param  array|string  $name
	 * @param  array  $rules
	 * @return $this
	 */
	public function arg($name, $rules = null)
	{
		$params = is_array($name) ? $name : [$name => $rules];

		foreach ($params as $name => $rules) {
			$this->args[$name] = $rules;
		}

		return $this;
	}
}
