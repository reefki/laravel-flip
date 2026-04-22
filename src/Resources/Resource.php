<?php

namespace Reefki\Flip\Resources;

use Reefki\Flip\Client;
use Reefki\Flip\Concerns\HasVersion;

abstract class Resource
{
    use HasVersion;

    /**
     * Underlying low-level HTTP client used to talk to Flip.
     *
     * @var \Reefki\Flip\Client
     */
    protected Client $client;

    /**
     * Build a new resource bound to the given client.
     *
     * @param  \Reefki\Flip\Client  $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Build a path prefixed with the resolved version segment.
     *
     * @param  string  $path  Path after the version segment, e.g. `general/balance`.
     * @param  string|null  $pinnedVersion  Version pinned by the calling resource (e.g. `v2`).
     * @return string
     */
    protected function path(string $path, ?string $pinnedVersion = null): string
    {
        $version = $this->resolveVersion($pinnedVersion);

        return '/' . trim($version, '/') . '/' . ltrim($path, '/');
    }

    /**
     * Drop nullable parameters and return the rest. Saves callers from
     * filtering optional fields manually.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    protected function compact(array $params): array
    {
        return array_filter($params, static fn ($v) => $v !== null);
    }
}
