<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\NamedType;
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

    protected string $endpoint;

    public function __construct(Schema $schema, EndpointConfig $endpointConfig, string $endpoint)
    {
        $this->schema = $schema;
        $this->endpointConfig = $endpointConfig;
        $this->endpoint = $endpoint;
    }

    /**
     * @return iterable<ClassType>
     */
    public function generate(): iterable
    {
        $typeConfigs = $this->endpointConfig->types($this->schema);

        foreach ($this->schema->getTypeMap() as $type) {
            if (! $type instanceof InputObjectType) {
                continue;
            }

            $class = new ClassType(
                $type->name,
                new PhpNamespace(static::inputsNamespace($this->endpointConfig))
            );

            $class->addExtend(Input::class);

            $converters = [];

            foreach ($type->getFields() as $name => $field) {
                $fieldType = $field->getType();

                /** @var Type&NamedType $namedType guaranteed since we pass in a non-null type */
                $namedType = Type::getNamedType($fieldType);

                $typeConfig = $typeConfigs[$namedType->name];

                $typeReference = $typeConfig->typeReference;

                $class->addComment('@property '.PhpType::phpDoc($fieldType, $typeReference) . ' $' . $name);

                $typeConverter = TypeConverterWrapper::wrap($fieldType, "new \\{$typeConfig->typeConverter}");
                $converters []= /** @lang PHP */"    '{$name}' => {$typeConverter}";
            }

            $convertersMethod = $class->addMethod('converters');
            $convertersString = implode(",\n", $converters);
            $convertersMethod->setBody(<<<PHP
return [
{$convertersString},
];
PHP
);
            $convertersMethod->setReturnType('array');

            $endpoint = $class->addMethod('endpoint');
            $endpoint->setStatic();
            $endpoint->setReturnType('string');
            $endpoint->setBody(<<<PHP
                            return '{$this->endpoint}';
                            PHP
            );

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
