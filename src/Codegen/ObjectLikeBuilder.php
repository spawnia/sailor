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

/** @phpstan-type PropertyArgs array{string, Type, string, string, mixed, bool} */
class ObjectLikeBuilder
{
    protected const IS_OMITTABLE_INDEX = 5;

    private bool $isInputType;

    private ClassType $class;

    private Method $make;

    private Method $converters;

    /** @var array<string, PropertyArgs> */
    private array $properties = [];

    public function __construct(string $name, string $namespace, bool $isInputType)
    {
        $class = new ClassType(
            Escaper::escapeClassName($name),
            new PhpNamespace($namespace) // TODO drop escape when min PHP version is 8.0+
        );

        $class->setExtends(ObjectLike::class);

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
    }

    /**
     * @param mixed $defaultValue any value
     * @param bool $isOmittable property data, merged across selections of the same field
     */
    public function addProperty(string $name, Type $type, string $phpDocType, string $typeConverter, $defaultValue, bool $isOmittable = false): void
    {
        // Fields may be referenced multiple times in a query through fragments, but they
        // are only included once in the result sent from the server, thus we eliminate duplicates here.
        if (isset($this->properties[$name])) {
            $this->properties[$name][self::IS_OMITTABLE_INDEX] = $this->properties[$name][self::IS_OMITTABLE_INDEX] && $isOmittable;

            return;
        }

        $this->properties[$name] = [$name, $type, $phpDocType, $typeConverter, $defaultValue, $isOmittable];
    }

    public function build(): ClassType
    {
        $requiredProperties = [];
        $optionalProperties = [];
        foreach ($this->properties as $args) {
            [, $type, , , $defaultValue, $isOmittable] = $args;
            if (self::resultType($type, $isOmittable) instanceof NonNull && $defaultValue === null) {
                $requiredProperties[] = $args;
            } else {
                $optionalProperties[] = $args;
            }
        }

        foreach (array_merge($requiredProperties, $optionalProperties) as $args) {
            $this->buildProperty(...$args);
        }

        $this->converters->addBody(/** @lang PHP */ '];');
        $this->make->addBody("\nreturn \$instance;");

        return $this->class;
    }

    /** @param mixed $defaultValue any value */
    protected function buildProperty(string $name, Type $type, string $phpDocType, string $typeConverter, $defaultValue, bool $isOmittable): void
    {
        $resultType = self::resultType($type, $isOmittable);
        $wrappedPhpDocType = TypeWrapper::phpDoc($resultType, $phpDocType, $this->isInputType);

        $this->class->addComment("@property {$wrappedPhpDocType} \${$name}");

        $wrappedTypeConverter = TypeWrapper::converter($type, "new \\{$typeConverter}");
        $omittableConverterClass = OmittableConverter::class;
        $fieldConverter = $isOmittable
            ? "new \\{$omittableConverterClass}({$wrappedTypeConverter})"
            : $wrappedTypeConverter;
        $this->converters->addBody(/** @lang PHP */ "    '{$name}' => {$fieldConverter},");

        if ($name === Introspection::TYPE_NAME_FIELD_NAME) {
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

    /** Omitted fields are null in the result, but present ones keep the non-null check of their converter. */
    protected static function resultType(Type $type, bool $isOmittable): Type
    {
        return $isOmittable && $type instanceof NonNull
            ? $type->getWrappedType()
            : $type;
    }
}
