<?php

namespace Arafatkn\WRest\Routing;

class Route
{
	public $methods;

	public $namespace = null;

	protected $domain = null;

	public $uri;

	public $action;

	protected $router;

	public function __construct($methods, $uri, $action)
	{
		$this->methods = (array) $methods;
		$this->uri = $uri;
		$this->action = $action;

		if (in_array('GET', $this->methods) && ! in_array('HEAD', $this->methods)) {
			$this->methods[] = 'HEAD';
		}
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
}
