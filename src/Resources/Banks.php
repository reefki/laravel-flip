<?php

namespace Reefki\Flip\Resources;

class Banks extends Resource
{
    /**
     * List supported banks with per-bank fee, queue length and status.
     *
     * Endpoint: `GET /{version}/general/banks`.
     *
     * @param  string|null  $code  Optional bank code to filter the list.
     * @return array<int, array{bank_code:string,name:string,fee:int,queue:int,status:string}>
     */
    public function list(?string $code = null): array
    {
        return $this->client->get($this->path('general/banks'), $this->compact(['code' => $code]));
    }

    /**
     * Convenience: fetch a single bank entry by code.
     *
     * @param  string  $code  Bank code (e.g. `bca`).
     * @return array{bank_code:string,name:string,fee:int,queue:int,status:string}|null
     */
    public function find(string $code): ?array
    {
        $list = $this->list($code);

        return $list[0] ?? null;
    }
}
