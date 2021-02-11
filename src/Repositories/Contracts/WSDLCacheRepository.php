<?php

namespace SIVI\AFDConnectors\Repositories\Contracts;

use Carbon\Carbon;

interface WSDLCacheRepository
{

    /**
     * @param $key
     * @return bool
     */
    public function has($key): bool;

    /**
     * @param $key
     * @return mixed
     */
    public function get($key);

    /**
     * @param $key
     * @param $value
     * @param Carbon $expiresAt
     */
    public function add($key, $value, Carbon $expiresAt): void;
}