<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use InvalidArgumentException;
use Kevin4845\LaravelFenerum\Enum\LookupField;
use Kevin4845\LaravelFenerum\Exception\InvalidResponseException;
use Kevin4845\LaravelFenerum\FenerumClient;
use Kevin4845\LaravelFenerum\Http\RawResponse;
use Kevin4845\LaravelFenerum\Support\Page;

abstract class Resource
{
    public function __construct(protected readonly FenerumClient $client)
    {
    }

    protected function path(string ...$segments): string
    {
        if ($segments === []) {
            throw new InvalidArgumentException('A Fenerum path needs at least one segment.');
        }

        $encoded = [];
        foreach ($segments as $segment) {
            if ($segment === '') {
                throw new InvalidArgumentException('Fenerum path segments must not be empty.');
            }

            $encoded[] = rawurlencode($segment);
        }

        return '/api/v1/' . implode('/', $encoded) . '/';
    }

    /**
     * @param array<string, scalar|null> $query
     * @param array<mixed>|\stdClass|null $json
     * @param array<string, string> $headers
     * @return array<mixed>|null
     */
    protected function call(
        string $method,
        string $path,
        array $query = [],
        array|\stdClass|null $json = null,
        array $headers = [],
    ): ?array {
        return $this->client->request($method, $path, $query, $json, $headers);
    }

    /**
     * @param array<string, scalar|null> $query
     * @param array<string, string> $headers
     */
    protected function page(string $path, array $query = [], array $headers = []): Page
    {
        $payload = $this->call('GET', $path, $query, null, $headers);
        if ($payload === null) {
            throw new InvalidResponseException('Expected a paginated Fenerum response.');
        }

        return Page::fromPayload($payload);
    }

    /**
     * @param array<string, scalar|null> $query
     * @param array<string, mixed>|\stdClass|null $json
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    protected function object(
        string $method,
        string $path,
        array $query = [],
        array|\stdClass|null $json = null,
        array $headers = [],
    ): array {
        $payload = $this->call($method, $path, $query, $json, $headers);
        if ($payload === null || array_is_list($payload)) {
            throw new InvalidResponseException('Expected a JSON object from Fenerum.');
        }

        return $payload;
    }

    /**
     * @param array<string, scalar|null> $query
     * @param array<string, string> $headers
     * @return list<array<string, mixed>>
     */
    protected function collection(string $path, array $query = [], array $headers = []): array
    {
        $payload = $this->call('GET', $path, $query, null, $headers);
        if ($payload === null || !array_is_list($payload)) {
            throw new InvalidResponseException('Expected a JSON array from Fenerum.');
        }

        $items = [];
        foreach ($payload as $item) {
            if (!is_array($item) || array_is_list($item)) {
                throw new InvalidResponseException('Expected a JSON array of objects from Fenerum.');
            }

            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param array<string, scalar|null> $query
     * @param array<mixed>|\stdClass|null $json
     * @param array<string, string> $headers
     */
    protected function nothing(
        string $method,
        string $path,
        array $query = [],
        array|\stdClass|null $json = null,
        array $headers = [],
    ): void {
        $payload = $this->call($method, $path, $query, $json, $headers);
        if ($payload !== null) {
            throw new InvalidResponseException('Expected an empty Fenerum response.');
        }
    }

    protected function emptyObject(): \stdClass
    {
        return new \stdClass();
    }

    /**
     * @param array<string, string> $headers
     * @return array<string, string>
     */
    protected function lookup(?LookupField $lookupField, array $headers = []): array
    {
        if ($lookupField !== null) {
            $headers['Lookup-Field'] = $lookupField->value;
        }

        return $headers;
    }

    /**
     * @param array<string, string> $headers
     * @return array<string, string>
     */
    protected function fields(?string $fields, array $headers = []): array
    {
        if ($fields !== null && $fields !== '') {
            $headers['Fields'] = $fields;
        }

        return $headers;
    }

    /**
     * @param array<string, scalar|null> $query
     * @param array<mixed>|\stdClass|null $json
     * @param array<string, string> $headers
     * @return list<array<string, mixed>>
     */
    protected function objectList(
        string $method,
        string $path,
        array $query = [],
        array|\stdClass|null $json = null,
        array $headers = [],
    ): array {
        $payload = $this->call($method, $path, $query, $json, $headers);
        if ($payload === null || !array_is_list($payload)) {
            throw new InvalidResponseException('Expected a JSON array of objects from Fenerum.');
        }

        $items = [];
        foreach ($payload as $item) {
            if (!is_array($item) || array_is_list($item)) {
                throw new InvalidResponseException('Expected a JSON array of objects from Fenerum.');
            }

            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param array<string, scalar|null> $query
     * @param array<string, string> $headers
     */
    protected function raw(string $method, string $path, array $query = [], array $headers = []): RawResponse
    {
        return $this->client->requestRaw($method, $path, $query, null, $headers);
    }
}
