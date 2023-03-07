<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use BenSampo\Enum\Enum;
use GraphQL\Type\Definition\Type;
use Nette\PhpGenerator\ClassLike;
use Nette\PhpGenerator\Method;
use Spawnia\Sailor\Codegen\Escaper;
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
        yield from parent::generateClasses();

        yield $this->makeTypeConverter($this->enumType, $this->endpointConfig);
    }

    protected function decorateTypeConverterClass(Type $type, ClassLike $class, Method $fromGraphQL, Method $toGraphQL): ClassLike
    {
        $customEnumClass = $this->enumClassName();

        $fromGraphQL->setReturnType($customEnumClass);
        $fromGraphQL->setBody(
            <<<PHP
                return new \\{$customEnumClass}(\$value);
                PHP
        );

        $toGraphQL->setReturnType('string');
        $toGraphQL->setBody(
            <<<PHP
                if (! \$value instanceof \\{$customEnumClass}) {
                    throw new \InvalidArgumentException('Expected instanceof {$customEnumClass}, got: '.gettype(\$value));
                }

                // @phpstan-ignore-next-line generated enum values are always strings
                return \$value->value;
                PHP
        );

        return $class;
    }

    protected function decorateEnumClass(ClassLike $class): ClassLike
    {
        $class->setExtends(Enum::class);

        foreach ($this->enumType->getValues() as $value) {
            $constName = Escaper::escapeMemberConstantName($value->name);
            $class->addComment("@method static static {$constName}()");
        }

        return parent::decorateEnumClass($class);
    }
}
