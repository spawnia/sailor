<?php

namespace Spawnia\Sailor;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Type\Schema;
use GraphQL\Validator\DocumentValidator;

class Validator
{
    /**
     * @var Schema
     */
    protected $schema;

    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    public function validate(DocumentNode $document)
    {
        return DocumentValidator::validate($this->schema, $document);
    }
}
