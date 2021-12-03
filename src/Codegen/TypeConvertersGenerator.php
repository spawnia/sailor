<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

class TypeConvertersGenerator extends ClassGenerator
{
    public function generate(): iterable
    {
        $class = new ClassType(
            'TypeConverters',
            new PhpNamespace($this->endpointConfig->namespace())
        );

        foreach ($this->endpointConfig->configureTypes($this->schema) as $name => $config) {
            $method = $class->addMethod($name);
            $method->setStatic(true);
            $method->setReturnType($config->typeConverter);
            $method->setBody(
                <<<PHP
                    static \$converter;

                    return \$converter ??= new \\{$config->typeConverter}();
                    PHP
            );
        }

        yield $class;
    }
}
