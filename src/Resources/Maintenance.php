<?php

namespace Reefki\Flip\Resources;

class Maintenance extends Resource
{
    /**
     * Whether Flip is currently down for maintenance.
     *
     * When true, all other Flip endpoints return HTTP 503.
     *
     * Endpoint: `GET /{version}/general/maintenance`.
     *
     * @return array{maintenance:bool}
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function check(): array
    {
        return $this->client->get($this->path('general/maintenance'));
    }

    /**
     * Boolean shortcut for the maintenance flag.
     *
     * @return bool
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function isUnderMaintenance(): bool
    {
        return (bool) ($this->check()['maintenance'] ?? false);
    }
}
