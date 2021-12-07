<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use InvalidArgumentException;
use Spawnia\Sailor\Convert\TypeConverter;
use Spawnia\Sailor\InvalidDataException;
use stdClass;

abstract class TypedObject implements TypeConverter
{
    /**
     * Necessary in order to be able to determine between explicit null and unset properties.
     *
     * @var array<string, mixed>
     */
    protected array $properties = [];

    /**
     * @return array<string, TypeConverter>
     */
    abstract protected function converters(): array;

    /**
     * Construct a new instance of itself using plain data.
     *
     * @return static
     */
    public static function fromStdClass(stdClass $data): self
    {
        static $instance;
        $instance ??= new static();

        return $instance->fromGraphQL($data);
    }

    /**
     * @param  mixed  $value  anything
     */
    public function __set(string $name, $value): void
    {
        $this->converter($name);

        $this->properties[$name] = $value;
    }

    /**
     * @return mixed anything
     */
    public function __get(string $name)
    {
        $this->converter($name);

        return $this->properties[$name];
    }

    public function __isset(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    public function toGraphQL($value): stdClass
    {
        if (! $value instanceof static) {
            $class = static::class;
            $notClass = gettype($value);

            throw new InvalidArgumentException("Expected instanceof {$class}, got: {$notClass}");
        }

        $serializable = new stdClass();

        foreach ($value->properties as $name => $property) {
            $serializable->{$name} = $this->converter($name)->toGraphQL($property);
        }

        return $serializable;
    }

    public function fromGraphQL($value)
    {
        if (! $value instanceof stdClass) {
            throw new InvalidArgumentException('Expected stdClass, got: ' . gettype($value));
        }

        $instance = new static();

        // @phpstan-ignore-next-line iteration over object
        foreach ($value as $name => $property) {
            // @phpstan-ignore-next-line variable property access
            $instance->{$name} = $this->converter($name)->fromGraphQL($property);
        }

        return $instance;
    }

    protected function converter(string $name): TypeConverter
    {
        $converters = $this->converters();
        if (! isset($converters[$name])) {
            $availableProperties = implode(
                ', ',
                array_keys($converters)
            );

            throw new InvalidDataException("Unknown property {$name}, available properties: {$availableProperties}.");
        }

        return $converters[$name];
    }
}
