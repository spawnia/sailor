<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Error\Error;
use GraphQL\Language\Parser;
use GraphQL\Utils\BuildSchema;
use PHPUnit\Framework\TestCase;
use Spawnia\Sailor\Codegen\Validator;

class ValidatorTest extends TestCase
{
    public function testValidateSuccess(): void
    {
        $schema = BuildSchema::build('
        type Query {
            foo: ID
        }
        ');

        $document = Parser::parse('
        {
            foo
        }
        ');
        Validator::validate($schema, $document);

        self::assertTrue(true);
    }

    public function testValidateFailure(): void
    {
        $schema = BuildSchema::build('
        type Query {
            foo: ID
        }
        ');

        $document = Parser::parse('
        {
            bar
        }
        ');

        $this->expectException(\Exception::class);
        Validator::validate($schema, $document);
    }
}
