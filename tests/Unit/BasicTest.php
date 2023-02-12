<?php

namespace Arafatkn\WRest\Tests\Unit;

use Arafatkn\WRest\Routing\Router;
use PHPUnit\Framework\TestCase;

function wrest() {
	return Router::resolveInstance();
}

final class BasicTest extends TestCase
{
	public function testWRest()
	{
		$uri = '/api/test';
		$uri2 = '/api/uri2';
		$callback = function () {
			return microtime(true);
		};

		wrest()->get($uri, $callback);
		wrest()->post($uri, $callback);
		wrest()->put($uri, $callback);
		wrest()->patch($uri, $callback);
		wrest()->delete($uri, $callback);

		wrest()->any($uri2, $callback);

		echo json_encode(wrest()->routes->routes, JSON_PRETTY_PRINT);
		echo json_encode(wrest()->routes->allRoutes, JSON_PRETTY_PRINT);
		ob_flush();

		$this->assertEquals('test', 'test');
	}
}
