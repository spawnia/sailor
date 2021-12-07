<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use GraphQL\Type\Definition\InputObjectField;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\NamedType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\Codegen\TypeWrapper;
use Spawnia\Sailor\EndpointConfig;

class InputTypeConfig implements TypeConfig
{
    private EndpointConfig $endpointConfig;

    private Schema $schema;

    private string $endpointName;

    private InputObjectType $inputObjectType;

    public function __construct(EndpointConfig $endpointConfig, Schema $schema, string $endpointName, InputObjectType $inputObjectType)
    {
        $this->endpointConfig = $endpointConfig;
        $this->schema = $schema;
        $this->endpointName = $endpointName;
        $this->inputObjectType = $inputObjectType;
    }

    /**
     * @return class-string<Input>
     */
    public function className(): string
    {
        // @phpstan-ignore-next-line Method Spawnia\Sailor\Codegen\InputGenerator::className() should return class-string<Spawnia\Sailor\Type\Input> but returns string.
        return $this->endpointConfig->typesNamespace() . '\\' . $this->inputObjectType->name;
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
        $typeConfigs = $this->endpointConfig->configureTypes($this->schema, $this->endpointName);

        $class = new ClassType(
            $this->inputObjectType->name,
            new PhpNamespace($this->endpointConfig->typesNamespace())
        );

        $class->addExtend(Input::class);

        $make = $class->addMethod('make');
        $make->setStatic(true);
        $make->setReturnType('self');
        $make->addBody("\$instance = new self;\n");

        $converters = [];

        foreach ($this->requiredFieldsFirst() as $name => $field) {
            $fieldType = $field->getType();

            /** @var Type&NamedType $namedType guaranteed since we pass in a non-null type */
            $namedType = Type::getNamedType($fieldType);

            $typeConfig = $typeConfigs[$namedType->name];

            $typeReference = $typeConfig->typeReference();
            $phpType = TypeWrapper::type($fieldType, $typeReference);
            $phpDoc = TypeWrapper::phpDoc($fieldType, $typeReference);

            $class->addComment("@property {$phpDoc} \${$name}");

            $typeConverter = TypeWrapper::converter($fieldType, "new \\{$typeConfig->typeConverter()}");
            $converters[] = /** @lang PHP */ "    '{$name}' => {$typeConverter}";

            $make->addComment("@param {$phpDoc} \${$name}");
            if ($field->isRequired()) {
                $make->addParameter($name)
                    ->setType($phpType);
            } else {
                // TODO deal with complex input values
                $make->addParameter($name, $field->defaultValue ?? null)
                    ->setType($phpType)
                    ->setNullable(true);
            }
            $make->addBody("\$instance->{$name} = \${$name};");
        }

        $make->addBody("\nreturn \$instance;");

        $convertersMethod = $class->addMethod('converters');
        $convertersString = implode(",\n", $converters);
        $convertersMethod->setBody(
            <<<PHP
                return [
                {$convertersString},
                ];
                PHP
        );
        $convertersMethod->setReturnType('array');

        $endpoint = $class->addMethod('endpoint');
        $endpoint->setStatic();
        $endpoint->setReturnType('string');
        $endpoint->setBody(
            <<<PHP
                return '{$this->endpointName}';
                PHP
        );

        yield $class;
    }

    /**
     * @return array<string, InputObjectField>
     */
    protected function requiredFieldsFirst(): array
    {
        $inputObjectFields = $this->inputObjectType->getFields();
        \Safe\uasort(
            $inputObjectFields,
            fn (InputObjectField $first, InputObjectField $second): int => (int) $second->isRequired() - (int) $first->isRequired()
        );

        return $inputObjectFields;
    }
}
