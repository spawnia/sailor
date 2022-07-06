<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Introspection;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\ObjectLike;

/**
 * @phpstan-type PropertyArgs array{string, Type, string, string, string, mixed}
 */
class ObjectLikeBuilder
{
    private ClassType $class;

    private Method $make;

    private Method $converters;

    /**
     * @var array<PropertyArgs>
     */
    private array $requiredProperties = [];

    /**
     * @var array<PropertyArgs>
     */
    private array $optionalProperties = [];

    public function __construct(string $name, string $namespace)
    {
        $class = new ClassType(
            Escaper::escapeName($name),
            new PhpNamespace(Escaper::escapeNamespace($namespace)) // Escape can be dropped when min PHP version is 8.0+
        );

        $class->addExtend(ObjectLike::class);

        $make = $class->addMethod('make');
        $make->setStatic(true);
        $make->setReturnType('self');
        $make->addBody("\$instance = new self;\n");
        $this->make = $make;

        $converters = $class->addMethod('converters');
        $converters->setProtected();
        $converters->setReturnType('array');
        $converters->addBody(
            <<<'PHP'
static $converters;

return $converters ??= [
PHP
        );
        $this->converters = $converters;

        $this->class = $class;
    }

    /**
     * @param mixed $defaultValue any value
     */
    public function addProperty(string $name, Type $type, string $phpDocType, string $phpType, string $typeConverter, $defaultValue): void
    {
        // Fields may be referenced multiple times in a query through fragments, but they
        // are only included once in the result sent from the server, thus we eliminate duplicates here.
        foreach (array_merge($this->requiredProperties, $this->optionalProperties) as [$existingName]) {
            if ($existingName === $name) {
                return;
            }
        }

        $args = [$name, $type, $phpDocType, $phpType, $typeConverter, $defaultValue];

        if ($type instanceof NonNull && null === $defaultValue) {
            $this->requiredProperties[] = $args;
        } else {
            $this->optionalProperties[] = $args;
        }
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

    /**
     * @param mixed $defaultValue any value
     */
    protected function buildProperty(string $name, Type $type, string $phpDocType, string $phpType, string $typeConverter, $defaultValue): void
    {
        $wrappedPhpDocType = TypeWrapper::phpDoc($type, $phpDocType);

        $this->class->addComment("@property {$wrappedPhpDocType} \${$name}");

        $typeConverter = Escaper::escapeNamespace($typeConverter);

        $wrappedTypeConverter = TypeWrapper::converter($type, "new \\{$typeConverter}");
        $this->converters->addBody(/** @lang PHP */ "    '{$name}' => {$wrappedTypeConverter},");

        if (Introspection::TYPE_NAME_FIELD_NAME === $name) {
            /** @var string $defaultValue set to parent type name in OperationGenerator */
            $this->make->addBody("\$instance->{$name} = '{$defaultValue}';");
        } else {
            $this->make->addComment("@param {$wrappedPhpDocType} \${$name}");

            $parameter = $this->make->addParameter($name);
            if (! $type instanceof NonNull || null !== $defaultValue) {
                $parameter->setNullable(true);
                $parameter->setDefaultValue(ObjectLike::UNDEFINED);
            }

            $this->make->addBody(
                <<<PHP
if (\${$name} !== self::UNDEFINED) {
    \$instance->{$name} = \${$name};
}
PHP
            );
        }
    }
}
