<?php

namespace Reefki\Flip\Resources;

use Reefki\Flip\Exceptions\ValidationException;

class Banks extends Resource
{
    /**
     * List supported banks with per-bank fee, queue length and status.
     *
     * Endpoint: `GET /{version}/general/banks`.
     *
     * @param  string|null  $code  Optional bank code to filter the list.
     * @return array<int, array{bank_code:string,name:string,fee:int,queue:int,status:string}>
     *
     * @throws \Reefki\Flip\Exceptions\ValidationException When `$code` is supplied and Flip does not recognize it.
     */
    public function list(?string $code = null): array
    {
        return $this->client->get($this->path('general/banks'), $this->compact(['code' => $code]));
    }

    /**
     * Convenience: fetch a single bank entry by code, or null when Flip
     * reports the code as unknown.
     *
     * @param  string  $code  Bank code (e.g. `bca`).
     * @return array{bank_code:string,name:string,fee:int,queue:int,status:string}|null
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function find(string $code): ?array
    {
        try {
            $list = $this->list($code);
        } catch (ValidationException $e) {
            if ($e->getMessage() === 'BANK_NOT_FOUND') {
                return null;
            }

            throw $e;
        }

        return $list[0] ?? null;
    }
}
