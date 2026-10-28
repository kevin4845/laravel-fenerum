<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Webhook;

use JsonException;
use Kevin4845\LaravelFenerum\Exception\InvalidWebhookPayload;

final class WebhookParser
{
    public function parse(string $body): WebhookEvent
    {
        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new InvalidWebhookPayload('Webhook body is not valid JSON.');
        }

        if (!is_array($decoded) || array_is_list($decoded)) {
            throw new InvalidWebhookPayload('Webhook body must be a JSON object.');
        }

        $event = $decoded['event'] ?? null;
        if (!is_string($event) || $event === '') {
            throw new InvalidWebhookPayload('Webhook body must contain a non-empty event name.');
        }

        if (!array_key_exists('data', $decoded) || !is_array($decoded['data'])) {
            throw new InvalidWebhookPayload('Webhook body must contain a data object.');
        }

        if (array_is_list($decoded['data']) && $decoded['data'] !== []) {
            throw new InvalidWebhookPayload('Webhook body must contain a data object.');
        }

        return new WebhookEvent($event, $decoded['data'], $decoded);
    }
}
