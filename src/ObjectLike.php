<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use Spawnia\Sailor\Convert\TypeConverter;
use Spawnia\Sailor\Error\InvalidDataException;

abstract class ObjectLike implements TypeConverter, BelongsToEndpoint
{
    public const UNDEFINED = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.';

    /**
     * Necessary in order to be able to determine between explicit null and unset properties.
     *
     * @var array<string, mixed>
     */
    protected array $properties = [];

    /** @return array<string, TypeConverter> */
    abstract protected function converters(): array;

    /**
     * Construct a new instance of itself using plain data.
     *
     * For mocking test data, prefer make().
     *
     * @return static
     */
    public static function fromStdClass(\stdClass $data): self
    {
        return (new static())->fromGraphQL($data);
    }

    /** Represent itself as plain data. */
    public function toStdClass(): \stdClass
    {
        return $this->toGraphQL($this);
    }

    /** @param mixed $value anything */
    public function __set(string $name, $value): void
    {
        // Validate the property exists
        $this->converter($name);

        $this->properties[$name] = $value;
    }

    /** @return mixed anything */
    public function __get(string $name)
    {
        // Validate the property exists
        $this->converter($name);

        // Optional properties in inputs might not be set, so we default them to null when explicitly requested.
        return $this->properties[$name] ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    public function toGraphQL($value): \stdClass
    {
        if (! $value instanceof static) {
            $class = static::class;
            $notClass = gettype($value);
            throw new \InvalidArgumentException("Expected instanceof {$class}, got: {$notClass}");
        }

        $serializable = new \stdClass();
        foreach ($value->properties as $name => $property) {
            $serializable->{$name} = $this->converter($name)->toGraphQL($property);
        }

        return $serializable;
    }

    /** @return static */
    public function fromGraphQL($value): self
    {
        if (! $value instanceof \stdClass) {
            $endpoint = static::endpoint();
            $notStdClass = gettype($value);
            throw new \InvalidArgumentException("{$endpoint}: Expected stdClass, got: {$notStdClass}");
        }

        $instance = new static();

        $converters = $this->converters();
        foreach ($converters as $name => $converter) {
            if (! property_exists($value, $name)) {
                $endpoint = static::endpoint();
                throw new InvalidDataException("{$endpoint}: Missing field {$name}.");
            }

            try {
                $instance->properties[$name] = $converter->fromGraphQL($value->{$name});
                unset($value->{$name});
            } catch (\Throwable $e) {
                $endpoint = static::endpoint();
                throw new InvalidDataException("{$endpoint}: Invalid value for field {$name}. {$e->getMessage()}");
            }
        }

        // @phpstan-ignore-next-line iteration over object
        foreach ($value as $name => $property) {
            throw static::unknownProperty($name, $converters);
        }

        return $instance;
    }

    protected function converter(string $name): TypeConverter
    {
        $converters = $this->converters();
        if (! isset($converters[$name])) {
            throw static::unknownProperty($name, $converters);
        }

        return $converters[$name];
    }

    /** @param array<string, TypeConverter> $converters */
    protected static function unknownProperty(string $name, array $converters): InvalidDataException
    {
        $endpoint = static::endpoint();
        $availableProperties = implode(', ', array_keys($converters));

        return new InvalidDataException("{$endpoint}: Unknown property {$name}, available properties: {$availableProperties}.");
    }
}
