<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use Nette\PhpGenerator\ClassType;

class ClassHelper
{
    public static function setConfig(ClassType $class, string $configFile): void
    {
        $method = $class->addMethod('config');
        $method->setStatic();
        $method->setReturnType('string');
        $method->setBody(
            <<<PHP
            return {$configFile};
            PHP
        );
    }

    public static function setEndpoint(ClassType $class, string $endpointName): void
    {
        $method = $class->addMethod('endpoint');
        $method->setStatic();
        $method->setReturnType('string');
        $method->setBody(
            <<<PHP
            return '{$endpointName}';
            PHP
        );
    }
}
