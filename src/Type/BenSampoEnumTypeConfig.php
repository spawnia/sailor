<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use BenSampo\Enum\Enum;
use GraphQL\Type\Definition\Type;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Spawnia\Sailor\Convert\GeneratesTypeConverter;

class BenSampoEnumTypeConfig extends EnumTypeConfig
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
                return new \\{$customEnumClass}(\$value);
                PHP
        );

        $toGraphQL->setBody(
            <<<PHP
                if (! \$value instanceof \\{$customEnumClass}) {
                    throw new \InvalidArgumentException('Expected instanceof {$customEnumClass}, got: '.gettype(\$value));
                }

                return \$value->value;
                PHP
        );

        return $class;
    }

    protected function decorateEnumClass(ClassType $class): ClassType
    {
        $class->addExtend(Enum::class);

        foreach ($this->enumType->getValues() as $value) {
            $class->addComment("@method static static {$value->name}()");
        }

        return parent::decorateEnumClass($class);
    }
}
