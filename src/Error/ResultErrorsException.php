<?php declare(strict_types=1);

namespace Spawnia\Sailor\Error;

use GraphQL\Error\ClientAware;

/**
 * TODO implement \GraphQL\Error\ProvidesExtensions once upgrading to graphql-php:15.
 */
class ResultErrorsException extends \Exception implements ClientAware
{
    use OriginatesFromEndpoint;

    /**
     * @var array<int, Error>
     */
    public array $errors;

    /**
     * @param array<int, Error> $errors
     */
    public function __construct(array $errors, string $configFile, string $endpointName)
    {
        $this->errors = $errors;
        $this->configFile = $configFile;
        $this->endpointName = $endpointName;

        $messages = implode(
            ' | ',
            array_map(
                static fn (Error $error): string => $error->message,
                $errors
            )
        );
        parent::__construct("{$configFile}({$endpointName}): {$messages}");
    }
}
