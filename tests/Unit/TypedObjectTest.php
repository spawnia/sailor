<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Simple\MyScalarQuery\MyScalarQuery;

class TypedObjectTest extends TestCase
{
    public function testDecode(): void
    {
        $data = (object) ['scalarWithArg' => 'bar'];
        $foo = MyScalarQuery::fromStdClass($data);

        self::assertSame('bar', $foo->scalarWithArg);
    }
}
