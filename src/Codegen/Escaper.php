<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use Nette\PhpGenerator\Helpers;
use function strtolower;

class Escaper
{
    public static function escapeClassName(string $name): string
    {
        // Inspiration from https://github.com/nette/php-generator/blob/f19b7975c7c4d729be5b64fce7eb72f0d4aac6fc/src/PhpGenerator/ClassLike.php#L87
        return isset(Helpers::Keywords[strtolower($name)])
            ? "_{$name}"
            : $name;
    }

    /**
     * TODO remove with PHP 8.
     */
    public static function escapeNamespaceName(string $name): string
    {
        return static::escapeClassName($name);
    }

    public static function escapeMemberConstantName(string $name): string
    {
        return 'class' === strtolower($name)
            ? "_{$name}"
            : $name;
    }
}
