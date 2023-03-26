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
		//
	}
}
