<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use PHPUnit\Framework\TestCase;
use GraphQL\Type\Definition\Type;
use Spawnia\Sailor\Codegen\PhpType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ListOfType;

/**
 * TODO https://github.com/spawnia/sailor/issues/1.
 */
class PhpTypeTest extends TestCase
{
    public function testSimpleType(): void
    {
        self::assertSame(
            'Foo|null',
            PhpType::phpDoc(
                Type::id(),
            'Foo'
            )
        );
    }

    public function testNonNullType(): void
    {
        self::assertSame(
            'Foo',
            PhpType::phpDoc(
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
            PhpType::phpDoc(
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
            PhpType::phpDoc(
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
