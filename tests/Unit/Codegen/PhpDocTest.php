<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use PHPUnit\Framework\TestCase;
use GraphQL\Type\Definition\Type;
use Spawnia\Sailor\Codegen\PhpDoc;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ListOfType;

/**
 * TODO https://github.com/spawnia/sailor/issues/1.
 */
class PhpDocTest extends TestCase
{
    public function testSimpleType(): void
    {
        self::assertSame(
            'Foo|null',
            PhpDoc::forType(
                Type::id(),
            'Foo'
            )
        );
    }

    public function testNonNullType(): void
    {
        self::assertSame(
            'Foo',
            PhpDoc::forType(
                new NonNull(
                    Type::id()
                ),
                'Foo'
            )
        );
    }

    public function testListOfType(): void
    {
        self::assertSame(
            'Foo[]|null',
            PhpDoc::forType(
                new ListOfType(
                    Type::id()
                ),
                'Foo'
            )
        );
    }

    public function testNonNullListOfType(): void
    {
        self::assertSame(
            'Foo[]',
            PhpDoc::forType(
                new NonNull(
                    new ListOfType(
                        Type::id()
                    )
                ),
                'Foo'
            )
        );
    }
}
