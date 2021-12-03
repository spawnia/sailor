<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Schema;
use Nette\PhpGenerator\ClassType;
use Spawnia\Sailor\Codegen\InputGenerator;
use Spawnia\Sailor\Type\TypeConfig;
use Spawnia\Sailor\TypeConverter\BooleanConverter;
use Spawnia\Sailor\TypeConverter\EnumConverter;
use Spawnia\Sailor\TypeConverter\FloatConverter;
use Spawnia\Sailor\TypeConverter\IDConverter;
use Spawnia\Sailor\TypeConverter\IntConverter;
use Spawnia\Sailor\TypeConverter\StringConverter;

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
    public function configureTypes(Schema $schema): array
    {
        $typeConverters = [
            'Int' => new TypeConfig(IntConverter::class, 'int'),
            'Float' => new TypeConfig(FloatConverter::class, 'float'),
            'String' => new TypeConfig(StringConverter::class, 'string'),
            'Boolean' => new TypeConfig(BooleanConverter::class, 'bool'),
            'ID' => new TypeConfig(IDConverter::class, 'string'),
        ];

        foreach ($schema->getTypeMap() as $name => $type) {
            if ($type instanceof EnumType) {
                $typeConverters[$name] = new TypeConfig(EnumConverter::class, 'string');
            } elseif ($type instanceof InputObjectType) {
                $inputClassName = InputGenerator::className($type, $this);
                $typeConverters[$name] = new TypeConfig($inputClassName, "\\{$inputClassName}");
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
}
