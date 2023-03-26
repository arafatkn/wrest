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
}
