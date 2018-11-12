<?php

namespace SIVI\AFDConnectors\Repositories\Contracts;

use SIVI\AFDConnectors\Exceptions\Exception;
use SIVI\AFDConnectors\Models\SKP\AuthToken;

interface SKPTokenRepository
{

    /**
     * @param string $appKey
     * @param string $username
     * @param string $password
     * @return AuthToken
     * @throws Exception
     */
    public function getToken($appKey, $username, $password);

}