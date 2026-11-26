<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Introspection;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\Convert\OmittableConverter;
use Spawnia\Sailor\ObjectLike;
use Spawnia\Sailor\OneOfInput;
use Spawnia\Sailor\Type\InputTypeConfig;
use Spawnia\Sailor\Type\TypeConfig;

/** @phpstan-type PropertyArgs array{string, Type, string, string, mixed, bool} */
class ObjectLikeBuilder
{
    private bool $isInputType;

    private bool $isOneOfVariant;

    private ClassType $class;

    private Method $make;

    private Method $converters;

    /** @var array<PropertyArgs> */
    private array $requiredProperties = [];

    /** @var array<PropertyArgs> */
    private array $optionalProperties = [];

    public function __construct(string $name, string $namespace, bool $isInputType, bool $isOneOfVariant = false)
    {
        $class = new ClassType(
            Escaper::escapeClassName($name),
            new PhpNamespace($namespace) // TODO drop escape when min PHP version is 8.0+
        );

        $class->setExtends($isOneOfVariant ? OneOfInput::class : ObjectLike::class);
        $class->setFinal($isOneOfVariant);

        $make = $class->addMethod('make');
        $make->setStatic(true);
        $make->setReturnType('self');
        $make->addBody("\$instance = new self;\n");
        $this->make = $make;

        $converters = $class->addMethod('converters');
        $converters->setProtected();
        $converters->setReturnType('array');
        $converters->addBody(<<<'PHP'
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
        PHP);
        $this->converters = $converters;

        $this->class = $class;
        $this->isInputType = $isInputType;
        $this->isOneOfVariant = $isOneOfVariant;
    }

    /** @param mixed $defaultValue any value */
    public function addProperty(string $name, Type $type, string $phpDocType, string $typeConverter, $defaultValue, bool $isOmittable): void
    {
        $args = [$name, $type, $phpDocType, $typeConverter, $defaultValue, $isOmittable];

        if ($type instanceof NonNull && $defaultValue === null && ! $isOmittable) {
            $this->requiredProperties[] = $args;
        } else {
            $this->optionalProperties[] = $args;
        }
    }

    /**
     * @param array<string, TypeConfig> $typeConfigs
     * @param mixed $defaultValue any value
     */
    public function addInputField(string $name, Type $type, array $typeConfigs, $defaultValue): void
    {
        $namedType = Type::getNamedType($type);
        assert($namedType !== null, 'guaranteed since we pass in a non-null type'); // @phpstan-ignore function.alreadyNarrowedType, notIdentical.alwaysTrue (keep for safety across graphql-php versions)

        $typeConfig = $typeConfigs[$namedType->name]; // @phpstan-ignore offsetAccess.invalidOffset (name is string, but typed as mixed in older graphql-php)
        assert($typeConfig instanceof InputTypeConfig);

        $this->addProperty(
            $name,
            $type,
            $typeConfig->inputTypeReference(),
            $typeConfig->typeConverter(),
            $defaultValue,
            false,
        );
    }

    public function build(): ClassType
    {
        foreach ($this->requiredProperties as $args) {
            $this->buildProperty(...$args);
        }

        foreach ($this->optionalProperties as $args) {
            $this->buildProperty(...$args);
        }

        $this->converters->addBody(/** @lang PHP */ '];');
        $this->make->addBody("\nreturn \$instance;");

        return $this->class;
    }

    /** @param mixed $defaultValue any value */
    protected function buildProperty(string $name, Type $type, string $phpDocType, string $typeConverter, $defaultValue, bool $isOmittable): void
    {
        $resultType = $isOmittable
            ? Type::getNullableType($type)
            : $type;
        $wrappedPhpDocType = TypeWrapper::phpDoc($resultType, $phpDocType, $this->isInputType);

        $this->class->addComment("@property {$wrappedPhpDocType} \${$name}");

        $wrappedTypeConverter = TypeWrapper::converter($type, "new \\{$typeConverter}");
        if ($isOmittable) {
            $omittableConverter = OmittableConverter::class;
            $wrappedTypeConverter = "new \\{$omittableConverter}({$wrappedTypeConverter})";
        }
        $this->converters->addBody(/** @lang PHP */ "    '{$name}' => {$wrappedTypeConverter},");

        if ($this->isOneOfVariant) {
            $this->make->addComment("@param {$wrappedPhpDocType} \$value");
            $this->make->addParameter('value');
            $this->make->addBody("\$instance->__set('{$name}', \$value);");
        } elseif ($name === Introspection::TYPE_NAME_FIELD_NAME) {
            assert(is_string($defaultValue), 'set to parent type name in OperationGenerator');
            $this->make->addBody("\$instance->{$name} = '{$defaultValue}';");
        } else {
            $this->make->addComment("@param {$wrappedPhpDocType} \${$name}");

            $parameter = $this->make->addParameter($name);
            if (! $resultType instanceof NonNull || $defaultValue !== null) {
                $parameter->setNullable(true);
                $parameter->setDefaultValue(ObjectLike::UNDEFINED);
            }

            // Call __set instead of just setting the property to avoid a naming conflict between
            // GraphQL fields named `properties` and the protected property `$properties` in ObjectLike.
            // See https://github.com/spawnia/sailor/issues/121.
            $this->make->addBody(<<<PHP
            if (\${$name} !== self::UNDEFINED) {
                \$instance->__set('{$name}', \${$name});
            }
            PHP);
        }
    }
}
