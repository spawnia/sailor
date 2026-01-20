<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Nette\PhpGenerator\ClassType;
use Spawnia\Sailor\Codegen\Escaper;
use Spawnia\Sailor\Codegen\ObjectLikeBuilder;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\ObjectLike;

/** @see https://spec.graphql.org/draft/#sec-Input-Objects */
class InputObjectTypeConfig implements TypeConfig, InputTypeConfig
{
    private EndpointConfig $endpointConfig;

    private Schema $schema;

    private InputObjectType $inputObjectType;

    public function __construct(EndpointConfig $endpointConfig, Schema $schema, InputObjectType $inputObjectType)
    {
        $this->endpointConfig = $endpointConfig;
        $this->schema = $schema;
        $this->inputObjectType = $inputObjectType;
    }

    /** @return class-string<ObjectLike> */
    public function className(): string
    {
        $namespace = $this->endpointConfig->typesNamespace();
        $className = Escaper::escapeClassName($this->inputObjectType->name);

        return "{$namespace}\\{$className}"; // @phpstan-ignore return.type (class-string not inferred)
    }

    public function typeConverter(): string
    {
        return $this->className();
    }

    public function inputTypeReference(): string
    {
        return "\\{$this->className()}";
    }

    /** @return iterable<ClassType> */
    public function generateClasses(): iterable
    {
        $typeConfigs = $this->endpointConfig->configureTypes($this->schema);

        $builder = new ObjectLikeBuilder(
            $this->inputObjectType->name,
            $this->endpointConfig->typesNamespace(),
            true,
        );

        foreach ($this->inputObjectType->getFields() as $name => $field) {
            $fieldType = $field->getType();

            $namedType = Type::getNamedType($fieldType);
            assert($namedType !== null, 'guaranteed since we pass in a non-null type'); // @phpstan-ignore function.alreadyNarrowedType, notIdentical.alwaysTrue (keep for safety across graphql-php versions)

            $typeConfig = $typeConfigs[$namedType->name]; // @phpstan-ignore offsetAccess.invalidOffset (name is string, but typed as mixed in older graphql-php)
            assert($typeConfig instanceof InputTypeConfig);

            $builder->addProperty(
                $name,
                $field->getType(),
                $typeConfig->inputTypeReference(),
                $typeConfig->typeConverter(),
                $field->defaultValue,
            );
        }

        yield $builder->build();
    }
}
