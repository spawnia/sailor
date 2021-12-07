<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Schema;
use Nette\PhpGenerator\ClassType;
use Spawnia\Sailor\Type\BooleanTypeConfig;
use Spawnia\Sailor\Type\EnumTypeConfig;
use Spawnia\Sailor\Type\FloatTypeConfig;
use Spawnia\Sailor\Type\IDTypeConfig;
use Spawnia\Sailor\Type\InputTypeConfig;
use Spawnia\Sailor\Type\IntTypeConfig;
use Spawnia\Sailor\Type\StringTypeConfig;
use Spawnia\Sailor\Type\TypeConfig;

abstract class EndpointConfig
{
    /**
     * Instantiate a client that will resolve the GraphQL operations.
     */
    abstract public function makeClient(): Client;

    /**
     * The namespace the generated classes will be created in.
     */
    abstract public function namespace(): string;

    /**
     * Path to the directory where the generated classes will be put.
     */
    abstract public function targetPath(): string;

    /**
     * Where to look for .graphql files containing operations.
     */
    abstract public function searchPath(): string;

    /**
     * The location of the schema file that describes the endpoint.
     */
    abstract public function schemaPath(): string;

    /**
     * Return a map from type names to a TypeConfig describing how to deal with them.
     *
     * @return array<string, TypeConfig>
     */
    public function configureTypes(Schema $schema, string $endpointName): array
    {
        $typeConverters = [
            'Int' => new IntTypeConfig(),
            'Float' => new FloatTypeConfig(),
            'String' => new StringTypeConfig(),
            'Boolean' => new BooleanTypeConfig(),
            'ID' => new IDTypeConfig(),
        ];

        foreach ($schema->getTypeMap() as $name => $type) {
            if ($type instanceof EnumType) {
                $typeConverters[$name] = new EnumTypeConfig();
            } elseif ($type instanceof InputObjectType) {
                $typeConverters[$name] = new InputTypeConfig($this, $schema, $endpointName, $type);
            }
        }

        return $typeConverters;
    }

    /**
     * Generate additional classes.
     *
     * Will overwrite built-in generated classes if named the same.
     *
     * @return iterable<ClassType>
     */
    public function generateClasses(Schema $schema, DocumentNode $document, string $endpointName): iterable
    {
        return [];
    }

    public function typesNamespace(): string
    {
        return $this->namespace() . '\\Types';
    }

    public function operationsNamespace(): string
    {
        return $this->namespace();
    }

    public function typeConvertersNamespace(): string
    {
        return $this->namespace() . '\\TypeConverters';
    }
}
