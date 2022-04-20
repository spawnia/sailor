<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit\Codegen;

use GraphQL\Language\Parser;
use GraphQL\Utils\BuildSchema;
use Spawnia\Sailor\Codegen\Validator;
use Spawnia\Sailor\Tests\TestCase;

final class ValidatorTest extends TestCase
{
    public function testValidateSuccess(): void
    {
        self::expectNotToPerformAssertions();

        $schema = BuildSchema::build(/** @lang GraphQL */ '
        type Query {
            simple: ID
        }
        ');

        $document = Parser::parse(/** @lang GraphQL */ '
        {
            simple
        }
        ');
        Validator::validate($schema, $document);
    }

    public function testValidateFailure(): void
    {
        $schema = BuildSchema::build(/** @lang GraphQL */ '
        type Query {
            simple: ID
        }
        ');

        $document = Parser::parse(/** @lang GraphQL */ '
        {
            bar
        }
        ');

        $this->expectException(\Exception::class);
        Validator::validate($schema, $document);
    }
}
