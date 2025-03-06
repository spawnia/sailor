<?php declare(strict_types=1);

namespace Spawnia\Sailor\PhpKeywords\Operations\AllCases;

/**
 * @property array<int, \Spawnia\Sailor\PhpKeywords\Operations\AllCases\Cases\_Case> $cases
 * @property string $__typename
 */
class AllCases extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<int, \Spawnia\Sailor\PhpKeywords\Operations\AllCases\Cases\_Case> $cases
     */
    public static function make($cases): self
    {
        $instance = new self;

        if ($cases !== self::UNDEFINED) {
            $instance->cases = $cases;
        }
        $instance->__typename = 'Query';

        return $instance;
    }

    protected function converters(): array
    {
        /** @var array<string, \Spawnia\Sailor\Convert\TypeConverter>|null $converters */
        static $converters;

        return $converters ??= [
            'cases' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\PhpKeywords\Operations\AllCases\Cases\_Case))),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'php-keywords';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../sailor.php');
    }
}
