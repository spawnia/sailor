<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\ObjectLike;
use Spawnia\Sailor\Operation;

/**
 * @phpstan-type PropertyArgs array{string, Type, string, string, mixed}
 */
class OperationBuilder
{
    private ClassType $class;

    private Method $execute;

    private Method $converters;

    /**
     * @var array<PropertyArgs>
     */
    private array $requiredVariables = [];

    /**
     * @var array<PropertyArgs>
     */
    private array $optionalVariables = [];

    public function __construct(string $name, string $namespace)
    {
        $class = new ClassType(
            Escaper::escapeClassName($name),
            new PhpNamespace($namespace)
        );

        // The execute method is the public API of the operation
        $execute = $class->addMethod('execute');
        $execute->setStatic(true);
        $execute->addBody('return self::executeOperation(');
        $this->execute = $execute;

        $converters = $class->addMethod('converters');
        $converters->setStatic(true);
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

    public function extendOperation(string $resultClass): void
    {
        $operationBaseClass = Operation::class;
        $this->class->setExtends($operationBaseClass);
        $this->class->setComment("@extends \\{$operationBaseClass}<\\{$resultClass}>");

        $this->execute->setReturnType($resultClass);
    }

    public function storeDocument(string $operationString): void
    {
        $document = $this->class->addMethod('document');
        $document->setStatic();
        $document->setReturnType('string');
        $document->setBody(
            <<<PHP
                                    return /* @lang GraphQL */ '{$operationString}';
                                    PHP
        );
    }

    /**
     * @param mixed $defaultValue any value
     */
    public function addVariable(string $name, Type $type, string $typeReference, string $typeConverter, $defaultValue): void
    {
        $args = [$name, $type, $typeReference, $typeConverter, $defaultValue];

        if ($type instanceof NonNull && null === $defaultValue) {
            $this->requiredVariables[] = $args;
        } else {
            $this->optionalVariables[] = $args;
        }
    }

    public function build(): ClassType
    {
        foreach ($this->requiredVariables as $args) {
            $this->buildVariable(...$args);
        }

        foreach ($this->optionalVariables as $args) {
            $this->buildVariable(...$args);
        }

        $this->converters->addBody(/** @lang PHP */ '];');
        $this->execute->addBody(');');

        return $this->class;
    }

    /**
     * @param mixed $defaultValue any value
     */
    protected function buildVariable(string $name, Type $type, string $typeReference, string $typeConverter, $defaultValue): void
    {
        $wrappedPhpDocType = TypeWrapper::phpDoc($type, $typeReference);

        $wrappedTypeConverter = TypeWrapper::converter($type, "new \\{$typeConverter}");

        /**
         * Not using a map because we utilize the numeric indizes to match parameters with arguments.
         *
         * @see Operation::fetchResponse()
         */
        $this->converters->addBody(/** @lang PHP */ "    ['{$name}', {$wrappedTypeConverter}],");

        $this->execute->addComment(/** @lang PHPDoc */ "@param {$wrappedPhpDocType} \${$name}");

        // TODO support default values properly
        $parameter = $this->execute->addParameter($name);
        if (! $type instanceof NonNull || null !== $defaultValue) {
            $parameter->setNullable(true);
            $parameter->setDefaultValue(ObjectLike::UNDEFINED);
        }

        $this->execute->addBody(/** @lang PHP */ "    \${$name},");
    }
}
