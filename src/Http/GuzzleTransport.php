<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Kevin4845\LaravelFenerum\Exception\NetworkException;

final class GuzzleTransport implements HttpTransport
{
    public function __construct(private readonly ClientInterface $client)
    {
    }

    public function send(
        string $method,
        string $uri,
        array $query,
        array|\stdClass|null $json,
        array $headers,
        float $timeout,
    ): HttpResponse {
        $options = [
            'http_errors' => false,
            'allow_redirects' => false,
            'timeout' => $timeout,
            'connect_timeout' => $timeout,
            'headers' => $headers,
        ];

        if ($query !== []) {
            $options['query'] = $query;
        }

        if ($json !== null) {
            $options['json'] = $json;
        }

        try {
            $response = $this->client->request($method, $uri, $options);
        } catch (RequestException $exception) {
            $response = $exception->getResponse();
            if ($response === null) {
                throw new NetworkException(
                    'Fenerum API request failed because the connection could not be completed.',
                );
            }
        } catch (GuzzleException) {
            throw new NetworkException(
                'Fenerum API request failed because the connection could not be completed.',
            );
        }

        $responseHeaders = [];
        foreach ($response->getHeaders() as $name => $values) {
            $responseHeaders[$name] = array_values($values);
        }

        return new HttpResponse($response->getStatusCode(), (string) $response->getBody(), $responseHeaders);
    }
}
