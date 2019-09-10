<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use Spawnia\Sailor\PhpDoc;
use PHPUnit\Framework\TestCase;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ObjectType;

/**
 * TODO https://github.com/spawnia/sailor/issues/1.
 */
class PhpDocTest extends TestCase
{
    public function testObjectType(): void
    {
        self::assertSame(
            'Foo|null',
            PhpDoc::forType(
                new ObjectType([
                    'name' => 'Foo',
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
                        'name' => 'Foo',
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
                        'name' => 'Foo',
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
                            'name' => 'Foo',
                        ])
                    )
                )
            )
        );
    }
}
