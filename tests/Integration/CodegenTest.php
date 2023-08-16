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
        $phpGeneratorVersion = Versions::getVersion('nette/php-generator');
        $phpGeneratorMajorVersion = (int) $phpGeneratorVersion[1];
        if ($phpGeneratorMajorVersion < 4) {
            self::markTestSkipped('Expectations for generated code only work with nette/php-generator 4 and above');
        }

        Examples::assertGeneratesExpectedCode($example);
    }

    /** @return iterable<array{string}> */
    public static function examples(): iterable
    {
        foreach (Examples::EXAMPLES as $example) {
            yield [$example];
        }
    }
}
