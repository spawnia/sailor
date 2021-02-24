<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Codegen\PhpType;

class PhpTypeTest extends TestCase
{
    public function testSimpleType(): void
    {
        self::assertSame(
            'MyScalarQuery|null',
            PhpType::phpDoc(
                Type::id(),
            'MyScalarQuery'
            )
        );
    }

    public function testNonNullType(): void
    {
        self::assertSame(
            'MyScalarQuery',
            PhpType::phpDoc(
                new NonNull(
                    Type::id()
                ),
                'MyScalarQuery'
            )
        );
    }

    public function testListOfType(): void
    {
        self::assertSame(
            'array<int, MyScalarQuery|null>|null',
            PhpType::phpDoc(
                new ListOfType(
                    Type::id()
                ),
                'MyScalarQuery'
            )
        );
    }

    public function testNonNullListOfNonNullTypes(): void
    {
        self::assertSame(
            'array<int, MyScalarQuery>',
            PhpType::phpDoc(
                new NonNull(
                    new ListOfType(
                        new NonNull(
                            Type::id()
                        )
                    )
                ),
                'MyScalarQuery'
            )
        );
    }

    public function testNonNullListOfListOfTypes(): void
    {
        self::assertSame(
            'array<int, array<int, MyScalarQuery|null>|null>',
            PhpType::phpDoc(
                new NonNull(
                    new ListOfType(
                        new ListOfType(
                            Type::id()
                        )
                    )
                ),
                'MyScalarQuery'
            )
        );
    }
}
