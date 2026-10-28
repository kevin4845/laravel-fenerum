<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

final class Exports extends Resource
{
    /**
     * GET /api/v1/exports/{id}/download/
     *
     * Response schema: ExportDownload. The schema does not say whether {id} is export_id or job_uuid
     * from ExportJob. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function download(string $id): array
    {
        return $this->object('GET', $this->path('exports', $id, 'download'), [], null);
    }
}
