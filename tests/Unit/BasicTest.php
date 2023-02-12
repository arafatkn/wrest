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
		$callback = function () {};

		wrest()->get($uri, $callback);
		wrest()->post($uri, $callback);
		wrest()->put($uri, $callback);
		wrest()->patch($uri, $callback);
		wrest()->delete($uri, $callback);
		wrest()->options($uri, $callback);

		$this->assertEquals(json_encode(wrest()->routes), 'test');
	}
}
