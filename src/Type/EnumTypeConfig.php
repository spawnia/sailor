<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use GraphQL\Type\Definition\EnumType;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\Convert\EnumConverter;
use Spawnia\Sailor\EndpointConfig;

class EnumTypeConfig implements TypeConfig
{
    protected EndpointConfig $endpointConfig;

    protected EnumType $enumType;

    public function __construct(EndpointConfig $endpointConfig, EnumType $enumType)
    {
        $this->endpointConfig = $endpointConfig;
        $this->enumType = $enumType;
    }

    public function typeConverter(): string
    {
        return EnumConverter::class;
    }

    public function typeReference(): string
    {
        return 'string';
    }

    /**
     * @return iterable<ClassType>
     */
    public function generateClasses(): iterable
    {
        yield $this->makeEnumClass();
    }

    public function enumClassName(): string
    {
        return $this->endpointConfig->typesNamespace() . '\\' . $this->enumType->name;
    }

    protected function makeEnumClass(): ClassType
    {
        $class = new ClassType(
            $this->enumType->name,
            new PhpNamespace($this->endpointConfig->typesNamespace())
        );

        return $this->decorateEnumClass($class);
    }

    protected function decorateEnumClass(ClassType $class): ClassType
    {
        foreach ($this->enumType->getValues() as $value) {
            $name = $value->name;
            $class->addConstant($name, $name);
        }

        return $class;
    }
}
