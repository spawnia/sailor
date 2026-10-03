<?php declare(strict_types=1);

namespace Spawnia\Sailor\Codegen;

use GraphQL\Language\AST\DirectiveNode;
use GraphQL\Type\Definition\Directive;

class ConditionalDirective
{
    /** @param iterable<DirectiveNode> $directives */
    public static function find(iterable $directives): ?string
    {
        foreach ($directives as $directive) {
            $name = $directive->name->value;
            if ($name === Directive::SKIP_NAME || $name === Directive::INCLUDE_NAME) {
                return $name;
            }
        }

        return null;
    }
}
