<?php

namespace Arafatkn\WRest\Routing;

use Arafatkn\WRest\Helpers\Arr;

class RouteCollection
{
	/**
	 * An array of the routes keyed by method.
	 *
	 * @var array
	 */
	public $routes = [];

	/**
	 * A flattened array of all the routes.
	 *
	 * @var Route[]
	 */
	public $allRoutes = [];

	/**
	 * Add a Route instance to the collection.
	 *
	 * @param  Route  $route
	 * @return Route
	 */
	public function add(Route $route)
	{
		$this->addToCollections($route);

		return $route;
	}

	/**
	 * Add the given route to the arrays of routes.
	 *
	 * @param  Route  $route
	 * @return void
	 */
	protected function addToCollections($route)
	{
		$domainAndUri = $route->getDomain().$route->uri();

		foreach ($route->methods() as $method) {
			$this->routes[$method][$domainAndUri] = $route;
		}

		$this->allRoutes[$method.'_'.$domainAndUri] = $route;
	}

	/**
	 * Get routes from the collection by method.
	 *
	 * @param  string|null  $method
	 * @return Route[]
	 */
	public function get($method = null)
	{
		return is_null($method) ? $this->getRoutes() : Arr::get($this->routes, $method, []);
	}

	/**
	 * Get all the routes in the collection.
	 *
	 * @return Route[]
	 */
	public function getRoutes()
	{
		return array_values($this->allRoutes);
	}

	/**
	 * Get all the routes keyed by their HTTP verb / method.
	 *
	 * @return array
	 */
	public function getRoutesByMethod()
	{
		return $this->routes;
	}
}
