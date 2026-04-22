<?php

namespace Reefki\Flip\Resources;

class Reference extends Resource
{
    /**
     * Read-only city code → city name map (Indonesian names).
     *
     * Pinned to v2 — Flip has not shipped a v3 equivalent.
     *
     * Endpoint: `GET /v2/disbursement/city-list`.
     *
     * @return array<string, string>
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function cities(): array
    {
        return $this->client->get($this->path('disbursement/city-list', 'v2'));
    }

    /**
     * Read-only country code → country name map (English names).
     *
     * Pinned to v2 — Flip has not shipped a v3 equivalent.
     *
     * Endpoint: `GET /v2/disbursement/country-list`.
     *
     * @return array<string, string>
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function countries(): array
    {
        return $this->client->get($this->path('disbursement/country-list', 'v2'));
    }

    /**
     * Combined city + country code → name map.
     *
     * Pinned to v2 — Flip has not shipped a v3 equivalent.
     *
     * Endpoint: `GET /v2/disbursement/city-country-list`.
     *
     * @return array<string, string>
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function citiesAndCountries(): array
    {
        return $this->client->get($this->path('disbursement/city-country-list', 'v2'));
    }
}
