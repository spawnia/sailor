<?php

namespace Spawnia\Sailor\Tests\Unit;

use GraphQL\Language\Parser;
use GraphQL\Utils\BuildSchema;
use Spawnia\Sailor\Validator;
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
