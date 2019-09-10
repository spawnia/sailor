<?php


namespace Spawnia\Sailor\Codegen;


class GeneratorOptions
{
    /**
     * Path to the directory where operations are located.
     *
     * @var string
     */
    public $searchPath;

    /**
     * Path to the directory where the output should be put.
     *
     * @var string
     */
    public $targetPath;

    /**
     * Base namespace to use in the generated classes.
     *
     * @var string
     */
    public $namespace;

    /**
     * Path to a schema file.
     *
     * @var string
     */
    public $schemaPath;
}
