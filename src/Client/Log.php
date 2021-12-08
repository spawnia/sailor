<?php declare(strict_types=1);

namespace Spawnia\Sailor\Client;

use Generator;
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

        $file = \Safe\fopen($this->filename, 'a');
        \Safe\fwrite($file, "{$log}\n");
        \Safe\fclose($file);

        return Response::fromStdClass((object) [
            'errors' => [
                (object) [
                    'message' => self::ERROR_MESSAGE,
                ],
            ],
        ]);
    }

    /**
     * @return Generator<int, array{
     *      query: string,
     *      variables: array<string, mixed>|null,
     * }>
     */
    public function requests(): Generator
    {
        $file = \Safe\fopen($this->filename, 'r');

        while ($line = fgets($file)) {
            // @phpstan-ignore-next-line we know the data in the log matches the defined array shape
            yield \Safe\json_decode($line, true);
        }

        \Safe\fclose($file);
    }

    public function clear(): void
    {
        if (file_exists($this->filename)) {
            \Safe\unlink($this->filename);
        }
    }
}
