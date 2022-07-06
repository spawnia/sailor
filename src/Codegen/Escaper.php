<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use function explode;
use function implode;
use Nette\PhpGenerator\Helpers;
use function strtolower;

class Escaper
{
    public static function escapeName(string $name): string
    {
        // Inspiration from https://github.com/nette/php-generator/blob/f19b7975c7c4d729be5b64fce7eb72f0d4aac6fc/src/PhpGenerator/ClassLike.php#L87
        if (isset(Helpers::Keywords[strtolower($name)])) {
            $name = '_' . $name;
        }

        return $name;
    }

    public static function escapeNamespace(string $name): string
    {
        $parts = explode('\\', $name);
        foreach ($parts as $i => $part) {
            $parts[$i] = self::escapeName($part);
        }

        return implode('\\', $parts);
    }
}
