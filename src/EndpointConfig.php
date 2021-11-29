<?php

declare(strict_types=1);

namespace Spawnia\Sailor;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Schema;
use Nette\PhpGenerator\ClassType;
use Spawnia\Sailor\Codegen\EnumGenerator;
use Spawnia\Sailor\Codegen\InputGenerator;
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
     * Map leaf types (scalar, enum) to a type converter.
     *
     * @return array<string, class-string<TypeConverter>>
     */
    public function typeConverters(Schema $schema): array
    {
        $typeConverters = [
            'Int' => IntConverter::class,
            'Float' => FloatConverter::class,
            'String' => StringConverter::class,
            'Boolean' => BooleanConverter::class,
            'ID' => IDConverter::class,
        ];

        foreach ($schema->getTypeMap() as $name => $type) {
            if ($type instanceof EnumType) {
                $typeConverters[$name] = EnumConverter::class;
            } elseif ($type instanceof InputObjectType) {
                $typeConverters[$name] = InputGenerator::className($type, $this);
            }
        }

        return $typeConverters;
    }

    public function enumGenerator(Schema $schema): EnumGenerator
    {
        return new EnumGenerator($schema, $this);
    }

    /**
     * @return iterable<ClassType>
     */
    public function generateClasses(Schema $schema, DocumentNode $document): iterable
    {
        return [];
    }
}
