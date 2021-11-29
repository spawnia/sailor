<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Type\Input;

class InputGenerator
{
    protected Schema $schema;

    protected EndpointConfig $endpointConfig;

    public function __construct(Schema $schema, EndpointConfig $endpointConfig)
    {
        $this->schema = $schema;
        $this->endpointConfig = $endpointConfig;
    }

    /**
     * @return iterable<ClassType>
     */
    public function generate(): iterable
    {
        foreach ($this->schema->getTypeMap() as $type) {
            if (! $type instanceof InputObjectType) {
                continue;
            }

            $class = new ClassType(
                $type->name,
                new PhpNamespace(static::inputsNamespace($this->endpointConfig))
            );

            $class->addExtend(Input::class);

            foreach ($type->getFields() as $name => $field) {
                $property = $class->addProperty($name);

                $fieldType = $field->getType();
                $namedType = Type::getNamedType($fieldType);

                if ($namedType instanceof ScalarType) {
                    $typeReference = PhpType::forScalar($namedType);
                } elseif ($namedType instanceof EnumType) {
                    $typeReference = PhpType::forEnum($namedType);
                } elseif ($namedType instanceof InputObjectType) {
                    $typeReference = '\\'.static::className($namedType, $this->endpointConfig);
                } else {
                    // @phpstan-ignore-next-line
                    throw new \Exception('Unsupported type '.get_class($namedType).' found.');
                }

                $property->setComment('@var '.PhpType::phpDoc($fieldType, $typeReference));
            }

            yield $class;
        }
    }

    /**
     * @return class-string<Input>
     */
    public static function className(InputObjectType $type, EndpointConfig $endpointConfig): string
    {
        // @phpstan-ignore-next-line Method Spawnia\Sailor\Codegen\InputGenerator::className() should return class-string<Spawnia\Sailor\Type\Input> but returns string.
        return self::inputsNamespace($endpointConfig).'\\'.$type->name;
    }

    protected static function inputsNamespace(EndpointConfig $endpointConfig): string
    {
        return $endpointConfig->namespace().'\\Inputs';
    }
}
