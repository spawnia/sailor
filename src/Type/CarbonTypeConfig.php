<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Spawnia\Sailor\Convert\GeneratesTypeConverter;
use Spawnia\Sailor\EndpointConfig;

class CarbonTypeConfig implements TypeConfig, InputTypeConfig, OutputTypeConfig
{
    use GeneratesTypeConverter;

    private EndpointConfig $endpointConfig;

    private ScalarType $scalarType;

    private string $format;

    public function __construct(EndpointConfig $endpointConfig, ScalarType $scalarType, string $format)
    {
        $this->endpointConfig = $endpointConfig;
        $this->scalarType = $scalarType;
        $this->format = $format;
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
        return '\\' . \Carbon\Carbon::class;
    }

    public function generateClasses(): iterable
    {
        yield $this->makeTypeConverter($this->scalarType, $this->endpointConfig);
    }

    protected function decorateTypeConverterClass(Type $type, ClassType $class, Method $fromGraphQL, Method $toGraphQL): ClassType
    {
        $carbonClass = \Carbon\Carbon::class;

        $fromGraphQL->setReturnType($carbonClass);
        $fromGraphQL->setBody(<<<PHP
        if (! is_string(\$value)) {
            throw new \InvalidArgumentException('Expected string, got: '.gettype(\$value));
        }

        \$date = \\{$carbonClass}::createFromFormat('{$this->format}', \$value);
        if (! \$date) { // @phpstan-ignore-line avoiding strict comparison, as different Carbon versions may return null or false
            throw new \InvalidArgumentException("Expected date with format {$this->format}, got {\$value}.");
        }

        return \$date;
        PHP);

        $toGraphQL->setBody(<<<PHP
        if (! \$value instanceof \\{$carbonClass}) {
            \$actualType = gettype(\$value);
            throw new \InvalidArgumentException("Expected instanceof {$carbonClass}, got {\$actualType}.");
        }

        return \$value->format('{$this->format}');
        PHP);

        return $class;
    }
}
