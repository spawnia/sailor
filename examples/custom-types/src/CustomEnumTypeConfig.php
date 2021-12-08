<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypesSrc;

use GraphQL\Type\Definition\Type;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Spawnia\Sailor\Convert\GeneratesTypeConverter;
use Spawnia\Sailor\Type\EnumTypeConfig;

class CustomEnumTypeConfig extends EnumTypeConfig
{
    use GeneratesTypeConverter;

    public function typeConverter(): string
    {
        return $this->typeConverterClassName($this->enumType, $this->endpointConfig);
    }

    public function typeReference(): string
    {
        return "\\{$this->enumClassName()}";
    }

    public function generateClasses(): iterable
    {
        foreach (parent::generateClasses() as $enum) {
            yield $enum;
        }

        yield $this->makeTypeConverter($this->enumType, $this->endpointConfig);
    }

    protected function decorateTypeConverterClass(Type $type, ClassType $class, Method $fromGraphQL, Method $toGraphQL): ClassType
    {
        $customEnumClass = $this->enumClassName();

        $fromGraphQL->setReturnType($customEnumClass);
        $fromGraphQL->setBody(
            <<<PHP
                if (! is_string(\$value)) {
                    throw new \InvalidArgumentException('Expected string, got: '.gettype(\$value));
                }

                return new \\{$customEnumClass}(\$value);
                PHP
        );

        $toGraphQL->setReturnType('string');
        $toGraphQL->setBody(
            <<<PHP
                if (! \$value instanceof \\{$customEnumClass}) {
                    throw new \InvalidArgumentException('Expected instanceof Enum, got: '.gettype(\$value));
                }

                return \$value->value;
                PHP
        );

        return $class;
    }

    protected function decorateEnumClass(ClassType $class): ClassType
    {
        $class->addExtend(Enum::class);

        return parent::decorateEnumClass($class);
    }
}
