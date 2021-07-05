<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Client;

use Spawnia\Sailor\Client;
use Spawnia\Sailor\Response;

class Log implements Client
{
    public const ERROR_MESSAGE = 'Request went to the Log client. It is only meant for testing and can not produce valid responses.';

    protected string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function request(string $query, \stdClass $variables = null): Response
    {
        $log = \Safe\json_encode([
            'query' => $query,
            'variables' => $variables,
        ]);

        $file = fopen($this->filename, 'a');
        fwrite($file, "{$log}\n");
        fclose($file);

        return Response::fromStdClass((object) [
            'errors' => [
                (object) [
                    'message' => self::ERROR_MESSAGE,
                ],
            ],
        ]);
    }
}
