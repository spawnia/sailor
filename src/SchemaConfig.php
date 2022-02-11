<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Schema;
use Nette\PhpGenerator\ClassType;
use Spawnia\Sailor\Error\Error;
use Spawnia\Sailor\Type\BooleanTypeConfig;
use Spawnia\Sailor\Type\EnumTypeConfig;
use Spawnia\Sailor\Type\FloatTypeConfig;
use Spawnia\Sailor\Type\IDTypeConfig;
use Spawnia\Sailor\Type\InputTypeConfig;
use Spawnia\Sailor\Type\IntTypeConfig;
use Spawnia\Sailor\Type\ScalarTypeConfig;
use Spawnia\Sailor\Type\StringTypeConfig;
use Spawnia\Sailor\Type\TypeConfig;
use stdClass;

abstract class SchemaConfig
{
    /**
     * Instantiate a client that will resolve the GraphQL operations.
     */
    abstract public function makeClient(): Client;

    /**
     * The location of the schema file that describes the endpoint.
     */
    abstract public function schemaPath(): string;
}
