<?php

namespace Arafatkn\WRest\Routing;

use Arafatkn\WRest\Helpers\Arr;

class Route
{
	public $methods;

	public $namespace = '';

	public $domain = null;

	public $uri;

	public $action;

	protected $router;

	public function __construct($methods, $uri, $action)
	{
		$this->methods = $methods;
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
	 * Get the name of the route instance.
	 *
	 * @return string|null
	 */
	public function getName()
	{
		return empty($this->action['as']) ? null : $this->action['as'];
	}

	/**
	 * Get the action name for the route.
	 *
	 * @return string
	 */
	public function getActionName()
	{
		return empty($this->action['controller']) ? 'Closure' : $this->action['controller'];
	}

	/**
	 * Get the method name of the route action.
	 *
	 * @return string
	 */
	public function getActionMethod()
	{
		return Arr::last(explode('@', $this->getActionName()));
	}

	/**
	 * Get the action array or one of its properties for the route.
	 *
	 * @param  string|null  $key
	 * @return mixed
	 */
	public function getAction($key = null)
	{
		return Arr::get($this->action, $key);
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
}