<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypesSrc;

use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Spawnia\Sailor\Convert\GeneratesTypeConverter;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\Type\InputTypeConfig;
use Spawnia\Sailor\Type\OutputTypeConfig;
use Spawnia\Sailor\Type\TypeConfig;

class CustomDateTypeConfig implements TypeConfig, InputTypeConfig, OutputTypeConfig
{
    use GeneratesTypeConverter;

    public const FORMAT = 'Y-m-d H:i:s';

    private EndpointConfig $endpointConfig;

    private ScalarType $scalarType;

    public function __construct(EndpointConfig $endpointConfig, ScalarType $scalarType)
    {
        $this->endpointConfig = $endpointConfig;
        $this->scalarType = $scalarType;
    }

    public function typeConverter(): string
    {
        return $this->typeConverterClassName($this->scalarType, $this->endpointConfig);
    }

    public function inputTypeReference(): string
    {
        return $this->typeReference();
    }

    public function outputTypeReference(): string
    {
        return $this->typeReference();
    }

    protected function typeReference(): string
    {
        return '\\' . \DateTime::class;
    }

    public function generateClasses(): iterable
    {
        yield $this->makeTypeConverter($this->scalarType, $this->endpointConfig);
    }

    protected function decorateTypeConverterClass(Type $type, ClassType $class, Method $fromGraphQL, Method $toGraphQL): ClassType
    {
        $dateTimeClass = \DateTime::class;
        $format = self::FORMAT;

        $fromGraphQL->setReturnType($dateTimeClass);
        $fromGraphQL->setBody(<<<PHP
        if (! is_string(\$value)) {
            throw new \InvalidArgumentException('Expected string, got: '.gettype(\$value));
        }

        \$date = \\{$dateTimeClass}::createFromFormat('{$format}', \$value);
        if (\$date === false) {
            throw new \InvalidArgumentException("Expected date with format {$format}, got {\$value}");
        }

        return \$date;
        PHP);

        $toGraphQL->setBody(<<<PHP
        if (! \$value instanceof \\{$dateTimeClass}) {
            throw new \InvalidArgumentException('Expected instanceof DateTime, got: '.gettype(\$value));
        }

        return \$value->format('{$format}');
        PHP);

        return $class;
    }
}
