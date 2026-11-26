<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\NullableType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\Codegen\Escaper;
use Spawnia\Sailor\Codegen\ObjectLikeBuilder;
use Spawnia\Sailor\Convert\OneOfInputConverter;
use Spawnia\Sailor\EndpointConfig;

/** @see https://spec.graphql.org/draft/#sec-OneOf-Input-Objects */
class OneOfInputTypeConfig implements TypeConfig, InputTypeConfig
{
    protected EndpointConfig $endpointConfig;

    protected Schema $schema;

    protected InputObjectType $inputObjectType;

    public function __construct(EndpointConfig $endpointConfig, Schema $schema, InputObjectType $inputObjectType)
    {
        $this->endpointConfig = $endpointConfig;
        $this->schema = $schema;
        $this->inputObjectType = $inputObjectType;
    }

    public static function isOneOf(InputObjectType $type): bool
    {
        if (method_exists($type, 'isOneOf') && $type->isOneOf() === true) { // @phpstan-ignore function.alreadyNarrowedType (graphql-php 14 does not have this method)
            return true;
        }

        // Older graphql-php versions do not populate isOneOf from SDL.
        if ($type->astNode !== null) {
            foreach ($type->astNode->directives as $directive) {
                if ($directive->name->value === 'oneOf') {
                    return true;
                }
            }
        }

        return false;
    }

    public function typeConverter(): string
    {
        return "{$this->variantNamespace()}\\Converter"; // @phpstan-ignore return.type (class-string not inferred)
    }

    public function inputTypeReference(): string
    {
        $namespace = $this->variantNamespace();
        $references = [];
        foreach ($this->variantNames() as $variant) {
            $references[] = "\\{$namespace}\\{$variant}";
        }

        return implode('|', $references);
    }

    public function generateClasses(): iterable
    {
        $namespace = $this->variantNamespace();
        $typeConfigs = $this->endpointConfig->configureTypes($this->schema);
        $names = $this->variantNames();
        $converter = new ClassType('Converter', new PhpNamespace($namespace));
        $converter->setExtends(OneOfInputConverter::class);
        $variants = $converter->addMethod('variants');
        $variants->setProtected();
        $variants->setReturnType('array');
        $variants->addBody('return [');

        foreach ($this->inputObjectType->getFields() as $name => $field) {
            $variant = $names[$name];
            $builder = new ObjectLikeBuilder($variant, $namespace, true, true);
            $type = $field->getType();
            if (! $type instanceof NullableType || $field->defaultValueExists()) {
                throw new \InvalidArgumentException("OneOf input field {$this->inputObjectType->name}.{$name} must be nullable and have no default value.");
            }

            // SDL fields are nullable so the other choices can be omitted. The selected field cannot be null.
            $builder->addInputField($name, Type::nonNull($type), $typeConfigs, null);
            yield $builder->build();

            $variants->addBody("    '{$name}' => {$variant}::class,");
        }

        $variants->addBody('];');
        yield $converter;
    }

    protected function variantNamespace(): string
    {
        $typeName = Escaper::escapeNamespaceName($this->inputObjectType->name);

        return "{$this->endpointConfig->typesNamespace()}\\{$typeName}";
    }

    /** @return array<string, string> */
    protected function variantNames(): array
    {
        $names = [];
        $used = [];
        $fields = array_keys($this->inputObjectType->getFields());
        \Safe\sort($fields, SORT_STRING); // @phpstan-ignore function.deprecated (still needed for PHP 7.4)
        /** @var list<string> $fields */
        foreach ($fields as $name) {
            $variant = 'By' . ucfirst($name);
            // GraphQL field names are case-sensitive; PHP class names are not.
            while (isset($used[strtolower($variant)])) {
                $variant .= '_';
            }

            $names[$name] = $variant;
            $used[strtolower($variant)] = true;
        }

        return $names;
    }
}
