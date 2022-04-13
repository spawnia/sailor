<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use Nette\PhpGenerator\ClassType;

class ClassHelper
{
    public static function setEndpoint(ClassType $class, string $endpointName): void
    {
        $endpoint = $class->addMethod('endpoint');
        $endpoint->setStatic();
        $endpoint->setReturnType('string');
        $endpoint->setBody(
            <<<PHP
            return '{$endpointName}';
            PHP
        );
    }
}
