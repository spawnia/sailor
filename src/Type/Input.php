<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\TypeConverter;

abstract class Input implements TypeConverter
{
    /**
     * Necessary in order to be able to determine between explicit null and unset properties.
     *
     * @var array<string, mixed>
     */
    protected array $properties = [];

    abstract public static function endpoint(): string;

    /**
     * @return array<string, TypeConverter>
     */
    abstract protected function converters(): array;

    public function __set(string $name, $value): void
    {
        $converters = $this->converters();
        if (! isset($converters[$name])) {
            throw new \InvalidArgumentException('Unknown property '.$name);
        }

        $this->properties[$name] = $value;
    }

    public function __get(string $name)
    {
        $converters = $this->converters();
        if (! isset($converters[$name])) {
            throw new \InvalidArgumentException('Unknown property '.$name);
        }

        return $this->properties[$name];
    }

    public function __isset(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    public function toGraphQL($value): \stdClass
    {
        /** @var static $value must not be called any other way */
        $converters = $this->converters();

        $serializable = new \stdClass();

        foreach ($value->properties as $name => $property) {
            $converter = $converters[$name];

            $serializable->{$name} = $converter->toGraphQL($property);
        }

        return $serializable;
    }

    /**
     * TODO consider splitting TypeConverter to avoid this useless stub?
     */
    public function fromGraphQL($value)
    {
        throw new \Exception('Unimplemented, since input objects are never in a result');
    }
}
