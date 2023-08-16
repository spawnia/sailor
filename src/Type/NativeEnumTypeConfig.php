<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use BenSampo\Enum\Enum;
use GraphQL\Type\Definition\EnumType as GraphQLEnumType;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\EnumType as PhpGeneratorEnumType;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\Codegen\Escaper;
use Spawnia\Sailor\Convert\NativeEnumConverter;
use Spawnia\Sailor\EndpointConfig;

/** Requires PHP 8.1 for native enum support. */
class NativeEnumTypeConfig implements TypeConfig, InputTypeConfig, OutputTypeConfig
{
    protected EndpointConfig $endpointConfig;

    protected GraphQLEnumType $enumType;

    public function __construct(EndpointConfig $endpointConfig, GraphQLEnumType $enumType)
    {
        $this->endpointConfig = $endpointConfig;
        $this->enumType = $enumType;
    }

    public function typeConverter(): string
    {
        // @phpstan-ignore-next-line PHPStan does not recognize the dynamically built class name
        return $this->endpointConfig->typeConvertersNamespace() . '\\' . $this->typeConverterBaseName();
    }

    public function inputTypeReference(): string
    {
        return $this->typeReference();
    }

    public function outputTypeReference(): string
    {
        return $this->typeReference();
    }

    public function generateClasses(): iterable
    {
        yield $this->makeEnumClass();
        yield $this->makeTypeConverter();
    }

    protected function typeReference(): string
    {
        return "\\{$this->enumClassName()}";
    }

    protected function makeTypeConverter(): ClassType
    {
        $class = new ClassType(
            $this->typeConverterBaseName(),
            new PhpNamespace($this->endpointConfig->typeConvertersNamespace())
        );

        $class->setExtends(NativeEnumConverter::class);

        $enumClass = $class->addMethod('enumClass');
        $enumClass->setVisibility('protected');
        $enumClass->setStatic(true);
        $enumClass->setReturnType('string');
        $enumClass->setBody("return \\{$this->enumClassName()}::class;");

        return $class;
    }

    protected function typeConverterBaseName(): string
    {
        return "{$this->enumType->name}Converter";
    }

    protected function enumClassBaseName(): string
    {
        return Escaper::escapeClassName($this->enumType->name);
    }

    /**
     * @return class-string<\BackedEnum>
     */
    protected function enumClassName(): string
    {
        // @phpstan-ignore-next-line PHPStan does not recognize the dynamically built class name
        return $this->endpointConfig->typesNamespace() . '\\' . $this->enumClassBaseName();
    }

    protected function makeEnumClass(): PhpGeneratorEnumType
    {
        $enum = new PhpGeneratorEnumType(
            $this->enumClassBaseName(),
            new PhpNamespace($this->endpointConfig->typesNamespace())
        );

        foreach ($this->enumType->getValues() as $value) {
            $enum->addCase($value->name, $value->name);
        }

        return $enum;
    }
}
