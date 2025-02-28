<?php declare(strict_types=1);

namespace Spawnia\Sailor\Tests\Integration;

use PackageVersions\Versions;
use Spawnia\Sailor\Tests\Examples;
use Spawnia\Sailor\Tests\TestCase;

final class CodegenTest extends TestCase
{
    /** @dataProvider examples */
    public function testGeneratesExpectedCode(string $example): void
    {
        Examples::assertGeneratesExpectedCode($example);
    }

    /** @return iterable<array{string}> */
    public static function examples(): iterable
    {
        foreach (Examples::EXAMPLES as $example) {
            yield [$example];
        }
    }

    public static function enumClassGenerationSupported(): bool
    {
        $phpGeneratorVersion = Versions::getVersion('nette/php-generator');
        $phpGeneratorMajorVersion = (int) $phpGeneratorVersion[1];

        return $phpGeneratorMajorVersion >= 4;
    }
}
