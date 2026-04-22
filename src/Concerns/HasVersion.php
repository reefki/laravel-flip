<?php

namespace Reefki\Flip\Concerns;

trait HasVersion
{
    /**
     * Per-resource API version override (e.g. `v2` or `v3`). When `null`, the
     * resource falls back to its pinned version, then the configured default.
     *
     * @var string|null
     */
    protected ?string $versionOverride = null;

    /**
     * Override the API version used for subsequent calls on this resource.
     *
     * Returns a clone so the original instance is not mutated.
     *
     * @param  string|null  $version  `v2` or `v3`. Pass `null` to revert to the configured default.
     * @return static
     */
    public function withVersion(?string $version): static
    {
        $clone = clone $this;
        $clone->versionOverride = $version;

        return $clone;
    }

    /**
     * Resolve the effective version, preferring the explicit override, then
     * the resource's pinned version (if any), then the configured default.
     *
     * @param  string|null  $pinned  Version pinned by the calling resource.
     * @return string
     */
    protected function resolveVersion(?string $pinned = null): string
    {
        return $this->versionOverride ?? $pinned ?? $this->client->defaultVersion();
    }
}
