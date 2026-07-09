<?php declare(strict_types=1);

namespace Spawnia\Sailor;

use GraphQL\Type\Definition\Argument;
use GraphQL\Type\Definition\HasFieldsType;
use GraphQL\Type\Definition\InputObjectField;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Introspection;
use GraphQL\Type\Schema;
use GraphQL\Utils\BuildClientSchema;
use GraphQL\Utils\SchemaPrinter;
use Spawnia\Sailor\Error\Error;
use Spawnia\Sailor\Error\ResultErrorsException;
use stdClass;

class Introspector
{
    protected EndpointConfig $endpointConfig;

    protected string $endpointName;

    protected string $configFile;

    public function __construct(EndpointConfig $endpointConfig, string $configFile, string $endpointName)
    {
        $this->endpointConfig = $endpointConfig;
        $this->configFile = $configFile;
        $this->endpointName = $endpointName;
    }

    public function introspect(): void
    {
        $client = $this->endpointConfig->makeClient();

        try {
            $introspectionResult = $this->fetchIntrospectionResult($client, true);
        } catch (\Throwable $_) {
            $introspectionResult = $this->fetchIntrospectionResult($client, false);
        }

        // @phpstan-ignore-next-line We know a stdClass converts to an associative array
        $introspection = Json::stdClassToAssoc($introspectionResult->data);
        assert(is_array($introspection));
        /** @var array<string, mixed> $introspection */

        $schema = BuildClientSchema::build($introspection);
        $this->restoreInputValueDeprecations($schema, $introspection);

        $schemaString = SchemaPrinter::doPrint($schema);

        \Safe\file_put_contents(
            $this->endpointConfig->schemaPath(),
            $schemaString
        );
    }

    protected function fetchIntrospectionResult(Client $client, bool $directiveIsRepeatable): Response
    {
        $response = $client->request(
            Introspection::getIntrospectionQuery([
                'directiveIsRepeatable' => $directiveIsRepeatable,
            ])
        );

        if (isset($response->errors)) {
            $parsedErrors = array_map(function (\stdClass $raw): Error {
                $parsed = $this->endpointConfig->parseError($raw);
                $parsed->configFile = $this->configFile;
                $parsed->endpointName = $this->endpointName;

                return $parsed;
            }, $response->errors);

            throw new ResultErrorsException($parsedErrors, $this->configFile, $this->endpointName);
        }

        return $response;
    }

    /** @param array<string, mixed> $introspection */
    protected function restoreInputValueDeprecations(Schema $schema, array $introspection): void
    {
        $schemaIntrospection = $introspection['__schema'] ?? null;
        if (! is_array($schemaIntrospection)) {
            return;
        }

        $types = $schemaIntrospection['types'] ?? null;
        if (is_array($types)) {
            foreach ($types as $typeIntrospection) {
                if (! is_array($typeIntrospection)) {
                    continue;
                }

                $typeName = $typeIntrospection['name'] ?? null;
                if (! is_string($typeName)) {
                    continue;
                }

                $type = $schema->getType($typeName);
                if ($type instanceof HasFieldsType) {
                    $fields = $typeIntrospection['fields'] ?? null;
                    if (is_array($fields)) {
                        foreach ($fields as $fieldIntrospection) {
                            if (! is_array($fieldIntrospection)) {
                                continue;
                            }

                            $fieldName = $fieldIntrospection['name'] ?? null;
                            if (! is_string($fieldName)) {
                                continue;
                            }

                            $field = $type->getFields()[$fieldName] ?? null;
                            $args = $fieldIntrospection['args'] ?? null;
                            if ($field !== null && is_array($args)) {
                                $this->restoreArgumentDeprecations($field->args, $args);
                            }
                        }
                    }
                }

                if ($type instanceof InputObjectType) {
                    $inputFields = $typeIntrospection['inputFields'] ?? null;
                    if (is_array($inputFields)) {
                        $this->restoreInputObjectFieldDeprecations($type->getFields(), $inputFields);
                    }
                }
            }
        }

        $directiveIntrospections = $schemaIntrospection['directives'] ?? null;
        if (! is_array($directiveIntrospections)) {
            return;
        }

        $directives = [];
        foreach ($schema->getDirectives() as $directive) {
            $directives[$directive->name] = $directive;
        }

        foreach ($directiveIntrospections as $directiveIntrospection) {
            if (! is_array($directiveIntrospection)) {
                continue;
            }

            $directiveName = $directiveIntrospection['name'] ?? null;
            $args = $directiveIntrospection['args'] ?? null;
            if (! is_string($directiveName) || ! isset($directives[$directiveName]) || ! is_array($args)) {
                continue;
            }

            $this->restoreArgumentDeprecations($directives[$directiveName]->args, $args);
        }
    }

    /**
     * @param  array<int, Argument>  $arguments
     * @param  array<mixed, mixed>  $argumentIntrospections
     */
    protected function restoreArgumentDeprecations(array $arguments, array $argumentIntrospections): void
    {
        $argumentsByName = [];
        foreach ($arguments as $argument) {
            $argumentsByName[$argument->name] = $argument;
        }

        foreach ($argumentIntrospections as $argumentIntrospection) {
            if (! is_array($argumentIntrospection)) {
                continue;
            }

            $argumentName = $argumentIntrospection['name'] ?? null;
            if (is_string($argumentName) && isset($argumentsByName[$argumentName])) {
                $this->restoreDeprecation($argumentsByName[$argumentName], $argumentIntrospection);
            }
        }
    }

    /**
     * @param  array<string, InputObjectField>  $inputObjectFields
     * @param  array<mixed, mixed>  $inputFieldIntrospections
     */
    protected function restoreInputObjectFieldDeprecations(array $inputObjectFields, array $inputFieldIntrospections): void
    {
        foreach ($inputFieldIntrospections as $inputFieldIntrospection) {
            if (! is_array($inputFieldIntrospection)) {
                continue;
            }

            $fieldName = $inputFieldIntrospection['name'] ?? null;
            if (is_string($fieldName) && isset($inputObjectFields[$fieldName])) {
                $this->restoreDeprecation($inputObjectFields[$fieldName], $inputFieldIntrospection);
            }
        }
    }

    /**
     * @param  Argument|InputObjectField  $inputValue
     * @param  array<mixed, mixed>  $inputValueIntrospection
     */
    protected function restoreDeprecation($inputValue, array $inputValueIntrospection): void
    {
        if (! array_key_exists('deprecationReason', $inputValueIntrospection)) {
            return;
        }

        $deprecationReason = $inputValueIntrospection['deprecationReason'];
        if ($deprecationReason !== null && ! is_string($deprecationReason)) {
            return;
        }

        $inputValue->deprecationReason = $deprecationReason;
        $inputValue->config['deprecationReason'] = $deprecationReason;
    }
}
