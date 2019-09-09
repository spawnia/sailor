<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Unit;

use GraphQL\Language\Parser;
use Spawnia\Sailor\Validator;
use GraphQL\Utils\BuildSchema;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testValidateSuccess()
    {
        $schema = BuildSchema::build('
        type Query {
            foo: ID
        }
        ');

        $validator = new Validator($schema);
        $document = Parser::parse('
        {
            foo
        }
        ');
        $errors = $validator->validate($document);

        $this->assertCount(0, $errors);
    }

    public function testValidateFailure()
    {
        $schema = BuildSchema::build('
        type Query {
            foo: ID
        }
        ');

        $validator = new Validator($schema);
        $document = Parser::parse('
        {
            bar
        }
        ');
        $errors = $validator->validate($document);

        $this->assertCount(1, $errors);
    }
}
