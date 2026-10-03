<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Introspection;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\Convert\OmittableConverter;
use Spawnia\Sailor\ObjectLike;

class ObjectLikeBuilder
{
    private bool $isInputType;

    private ClassType $class;

    private Method $make;

    private Method $converters;

    /** @var array<string, PropertyDefinition> */
    protected array $properties = [];

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
     * @param bool $isOmittable whether this selection may be omitted, the field is only omittable if all its selections are
     */
    public function addProperty(string $name, Type $type, string $phpDocType, string $typeConverter, $defaultValue, bool $isOmittable): void
    {
        // Fields may be referenced multiple times in a query through fragments, but they
        // are only included once in the result sent from the server, thus we eliminate duplicates here.
        $existingProperty = $this->properties[$name] ?? null;
        if ($existingProperty !== null) {
            $existingProperty->isOmittable = $existingProperty->isOmittable && $isOmittable;

            return;
        }

        $this->properties[$name] = new PropertyDefinition($name, $type, $phpDocType, $typeConverter, $defaultValue, $isOmittable);
    }

    public function build(): ClassType
    {
        $requiredProperties = [];
        $optionalProperties = [];
        foreach ($this->properties as $property) {
            if ($property->isRequired()) {
                $requiredProperties[] = $property;
            } else {
                $optionalProperties[] = $property;
            }
        }

        foreach (array_merge($requiredProperties, $optionalProperties) as $property) {
            $this->buildProperty($property);
        }

        $this->converters->addBody(/** @lang PHP */ '];');
        $this->make->addBody("\nreturn \$instance;");

        return $this->class;
    }

    protected function buildProperty(PropertyDefinition $property): void
    {
        $name = $property->name;
        $wrappedPhpDocType = TypeWrapper::phpDoc($property->resultType(), $property->phpDocType, $this->isInputType);

        $this->class->addComment("@property {$wrappedPhpDocType} \${$name}");

        $wrappedTypeConverter = TypeWrapper::converter($property->type, "new \\{$property->typeConverter}");
        $omittableConverterClass = OmittableConverter::class;
        $fieldConverter = $property->isOmittable
            ? "new \\{$omittableConverterClass}({$wrappedTypeConverter})"
            : $wrappedTypeConverter;
        $this->converters->addBody(/** @lang PHP */ "    '{$name}' => {$fieldConverter},");

        if ($name === Introspection::TYPE_NAME_FIELD_NAME) {
            $typeName = $property->defaultValue;
            assert(is_string($typeName), 'set to parent type name in OperationGenerator');
            $this->make->addBody("\$instance->{$name} = '{$typeName}';");
        } else {
            $this->make->addComment("@param {$wrappedPhpDocType} \${$name}");

            $parameter = $this->make->addParameter($name);
            if (! $property->isRequired()) {
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
