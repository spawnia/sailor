<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

use GraphQL\Utils\Utils;

abstract class NativeEnumConverter implements TypeConverter
{
    /** @var class-string<\UnitEnum> */
    protected string $enumClass;

    /** @var array<\UnitEnum> */
    protected array $cases;

    public function __construct()
    {
        $this->enumClass = static::enumClass();
        $this->cases = $this->enumClass::cases();
    }

    public function fromGraphQL($value): \UnitEnum
    {
        foreach ($this->cases as $case) {
            if ($case->name === $value) {
                return $case;
            }
        }

        $names = implode(', ', array_map(fn (\UnitEnum $case): string => $case->name, $this->cases));
        $notName = Utils::printSafeJson($value);
        throw new \InvalidArgumentException("Expected one of [{$names}], got {$notName}.");
    }

    public function toGraphQL($value): string
    {
        if (! $value instanceof \UnitEnum) {
            $notEnum = gettype($value);
            throw new \InvalidArgumentException("Expected \UnitEnum, got {$notEnum}.");
        }

        $enumClass = get_class($value);
        if ($enumClass !== $this->enumClass) {
            $notEnum = get_class($value);
            throw new \InvalidArgumentException("Expected instanceof {$this->enumClass}, got {$notEnum}.");
        }

        return $value->name;
    }

    /** @return class-string<\UnitEnum> */
    abstract protected static function enumClass(): string;
}
