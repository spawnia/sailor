<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Type\Schema;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\EndpointConfig;

class TypeConvertersGenerator implements ClassGenerator
{
    protected Schema $schema;

    protected DocumentNode $document;

    protected EndpointConfig $endpointConfig;

    public function __construct(Schema $schema, EndpointConfig $endpointConfig)
    {
        $this->schema = $schema;
        $this->endpointConfig = $endpointConfig;
    }

    public function generate(): iterable
    {
        $class = new ClassType(
            'TypeConverters',
            new PhpNamespace($this->endpointConfig->namespace())
        );

        foreach ($this->endpointConfig->configureTypes($this->schema) as $name => $config) {
            $method = $class->addMethod($name);
            $method->setStatic(true);
            $method->setReturnType($config->typeConverter());
            $method->setBody(
                <<<PHP
                    static \$converter;

                    return \$converter ??= new \\{$config->typeConverter()}();
                    PHP
            );
        }

        yield $class;
    }
}
