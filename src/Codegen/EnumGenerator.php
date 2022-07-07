<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Type\Definition\EnumType;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\EndpointConfig;

class EnumGenerator implements ClassGenerator
{
    private EndpointConfig $endpointConfig;

    private EnumType $enumType;

    public function __construct(EndpointConfig $endpointConfig, EnumType $enumType)
    {
        $this->endpointConfig = $endpointConfig;
        $this->enumType = $enumType;
    }

    /**
     * @return iterable<ClassType>
     */
    public function generate(): iterable
    {
        $class = $this->makeClass();

        yield $this->decorateClass($class);
    }

    public function className(): string
    {
        return $this->endpointConfig->typesNamespace() . '\\' . Escaper::escapeClassName($this->enumType->name);
    }

    protected function makeClass(): ClassType
    {
        return new ClassType(
            Escaper::escapeClassName($this->enumType->name),
            new PhpNamespace($this->endpointConfig->typesNamespace())
        );
    }

    protected function decorateClass(ClassType $class): ClassType
    {
        foreach ($this->enumType->getValues() as $value) {
            $name = $value->name;
            $class->addConstant(
                Escaper::escapeMemberConstantName($name),
                $name
            );
        }

        return $class;
    }
}
