<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\NamedType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Nette\PhpGenerator\ClassType;
use Spawnia\Sailor\Codegen\Escaper;
use Spawnia\Sailor\Codegen\ObjectLikeBuilder;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\ObjectLike;

class InputTypeConfig implements TypeConfig
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

    /**
     * @return class-string<ObjectLike>
     */
    public function className(): string
    {
        // @phpstan-ignore-next-line Method Spawnia\Sailor\Codegen\InputGenerator::className() should return class-string<Spawnia\Sailor\Type\Input> but returns string.
        return $this->endpointConfig->typesNamespace() . '\\' . Escaper::escapeClassName($this->inputObjectType->name);
    }

    public function typeConverter(): string
    {
        return $this->className();
    }

    public function typeReference(): string
    {
        return "\\{$this->className()}";
    }

    /**
     * @return iterable<ClassType>
     */
    public function generateClasses(): iterable
    {
        $typeConfigs = $this->endpointConfig->configureTypes($this->schema);

        $builder = new ObjectLikeBuilder(
            $this->inputObjectType->name,
            $this->endpointConfig->typesNamespace(),
        );

        foreach ($this->inputObjectType->getFields() as $name => $field) {
            $fieldType = $field->getType();

            /** @var Type&NamedType $namedType guaranteed since we pass in a non-null type */
            $namedType = Type::getNamedType($fieldType);
            $typeConfig = $typeConfigs[$namedType->name];

            $typeReference = $typeConfig->typeReference();
            $builder->addProperty(
                $name,
                $field->getType(),
                $typeReference,
                $typeReference,
                $typeConfig->typeConverter(),
                $field->defaultValue,
            );
        }

        yield $builder->build();
    }
}
