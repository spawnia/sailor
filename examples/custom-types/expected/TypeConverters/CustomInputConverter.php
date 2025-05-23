<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypes\TypeConverters;

class CustomInputConverter implements \Spawnia\Sailor\Convert\TypeConverter
{
    public function fromGraphQL($value): \Spawnia\Sailor\CustomTypesSrc\CustomObject
    {
        if (! $value instanceof \stdClass) {
            throw new \InvalidArgumentException('Expected stdClass, got: '.gettype($value));
        }

        if (! property_exists($value, 'foo')) {
            throw new \InvalidArgumentException('Did not find expected property foo.');
        }

        $foo = $value->foo;
        if (! is_string($foo)) {
            $fooType = gettype($foo);
            throw new \InvalidArgumentException("Expected foo to be string, got: {$fooType}.");
        }

        return new \Spawnia\Sailor\CustomTypesSrc\CustomObject($foo);
    }

    public function toGraphQL($value)
    {
        if (! $value instanceof \Spawnia\Sailor\CustomTypesSrc\CustomObject) {
            throw new \InvalidArgumentException('Expected instanceof Spawnia\Sailor\CustomTypesSrc\CustomObject, got: '.gettype($value));
        }

        return (object) (array) $value;
    }

    public static function endpoint(): string
    {
        return 'custom-types';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../sailor.php');
    }
}
