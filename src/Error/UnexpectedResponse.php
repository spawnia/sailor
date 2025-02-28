<?php declare(strict_types=1);

namespace Spawnia\Sailor\Error;

use Psr\Http\Message\ResponseInterface;

class UnexpectedResponse extends \Exception
{
    public int $statusCode;

    public string $responseBody;

    /** @var array<string, array<string>> */
    public array $responseHeaders;

    /** @param array<string, array<string>> $responseHeaders */
    public function __construct(
        string $message,
        int $statusCode,
        string $responseBody,
        array $responseHeaders
    ) {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->responseBody = $responseBody;
        $this->responseHeaders = $responseHeaders;
    }

    public static function statusCode(ResponseInterface $response): self
    {
        $statusCode = $response->getStatusCode();
        $responseBody = $response->getBody()->__toString();
        $responseHeaders = $response->getHeaders();

        $jsonEncodedHeaders = \Safe\json_encode($responseHeaders, JSON_PRETTY_PRINT);

        return new self(
            "Unexpected HTTP status code received: {$statusCode}.\nReason:\n{$responseBody}\nHeaders:\n{$jsonEncodedHeaders}",
            $statusCode,
            $responseBody,
            $responseHeaders,
        );
    }
}
