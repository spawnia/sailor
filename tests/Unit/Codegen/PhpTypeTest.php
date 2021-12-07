<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Codegen\TypeWrapper;

class PhpTypeTest extends TestCase
{
    public function testSimpleType(): void
    {
        self::assertSame(
            'MyScalarQuery|null',
            TypeWrapper::phpDoc(
                Type::id(),
                'MyScalarQuery'
            )
        );
    }

    public function testNonNullType(): void
    {
        self::assertSame(
            'MyScalarQuery',
            TypeWrapper::phpDoc(
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
            TypeWrapper::phpDoc(
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
            TypeWrapper::phpDoc(
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
            TypeWrapper::phpDoc(
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
