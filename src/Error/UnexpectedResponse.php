<?php declare(strict_types=1);

namespace Spawnia\Sailor\Error;

use Psr\Http\Message\ResponseInterface;

class UnexpectedResponse extends \Exception
{
    public int $statusCode = 0;

    public string $responseBody = '';

    /** @var array<string, list<string>> */
    public array $responseHeaders;

    public static function statusCode(ResponseInterface $response): self
    {
        $statusCode = $response->getStatusCode();
        $responseBody = $response->getBody()->__toString();

        $jsonEncodedHeaders = \Safe\json_encode($response->getHeaders(), JSON_PRETTY_PRINT);

        $self = new self(
            \Safe\sprintf(
                "Unexpected HTTP status code received: %d. Reason: \n%s\nHeaders:\n"
                . $jsonEncodedHeaders,
                $statusCode,
                $responseBody,
            ),
        );
        $self->statusCode = $statusCode;
        $self->responseHeaders = $response->getHeaders(); // @phpstan-ignore assign.propertyType
        $self->responseBody = $responseBody;

        return $self;
    }
}
