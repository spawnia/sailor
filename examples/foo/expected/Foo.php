<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Foo;

use Spawnia\Sailor\Foo\Foo\FooResult;

class Foo extends \Spawnia\Sailor\Operation
{
	const DOCUMENT = "query Foo {\n    foo\n}\n";

	public static function run(): FooResult
	{
	    $instance = new self;

		return $instance->runInternal(self::DOCUMENT);
	}
}
