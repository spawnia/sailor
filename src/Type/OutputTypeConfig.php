<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

/**
 * Specifies how Sailor should deal with a GraphQL output type.
 *
 * https://spec.graphql.org/draft/#sec-Input-and-Output-Types
 */
interface OutputTypeConfig
{
    /**
     * Reference to the type for usage in PHPDocs, e.g. string, \Foo\Bar.
     *
     * Make sure that class names begin with a backslash and are thus fully qualified.
     */
    public function outputTypeReference(): string;
}
