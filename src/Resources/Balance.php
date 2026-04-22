<?php

namespace Reefki\Flip\Resources;

class Balance extends Resource
{
    /**
     * Fetch the current Flip account balance in IDR.
     *
     * Endpoint: `GET /{version}/general/balance`.
     *
     * @return array{balance:int}
     */
    public function get(): array
    {
        return $this->client->get($this->path('general/balance'));
    }
}
