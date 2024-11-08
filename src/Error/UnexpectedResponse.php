<?php declare(strict_types=1);

namespace Spawnia\Sailor\Error;

use Psr\Http\Message\ResponseInterface;

class UnexpectedResponse extends \Exception
{
    public int $statusCode = 0;

    public string $responseBody = '';

    /** @var array<string, array<string>> */
    public array $responseHeaders;

    public function __construct(ResponseInterface $response)
    {
        $statusCode = $response->getStatusCode();
        $responseBody = $response->getBody()->__toString();
        $responseHeaders = $response->getHeaders();

        $this->statusCode = $statusCode;
        $this->responseBody = $responseBody;
        $this->responseHeaders = $responseHeaders;

        parent::__construct(
            "Unexpected response received: {$statusCode}. Reason: \n{$responseBody}\nHeaders:\n" . \Safe\json_encode($responseHeaders, \JSON_PRETTY_PRINT),
        );
    }
}
