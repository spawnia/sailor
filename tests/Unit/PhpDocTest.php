<?php

namespace Spawnia\Sailor\Tests\Unit;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\PhpDoc;

/**
 * TODO https://github.com/spawnia/sailor/issues/1
 */
class PhpDocTest extends TestCase
{
    public function testObjectType(): void
    {
        self::assertSame(
            'Foo|null',
            PhpDoc::forType(
                new ObjectType([
                    'name' => 'Foo'
                ])
            )
        );
    }

    public function testNonNullObjectType(): void
    {
        self::assertSame(
            'Foo',
            PhpDoc::forType(
                new NonNull(
                    new ObjectType([
                        'name' => 'Foo'
                    ])
                )
            )
        );
    }

    public function testListObjectType(): void
    {
        self::assertSame(
            'Foo[]|null',
            PhpDoc::forType(
                new ListOfType(
                    new ObjectType([
                        'name' => 'Foo'
                    ])
                )
            )
        );
    }

    public function testNonNullListObjectType(): void
    {
        self::assertSame(
            'Foo[]',
            PhpDoc::forType(
                new NonNull(
                    new ListOfType(
                        new ObjectType([
                            'name' => 'Foo'
                        ])
                    )
                )
            )
        );
    }
}
