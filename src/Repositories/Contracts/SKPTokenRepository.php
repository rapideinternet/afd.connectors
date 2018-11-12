<?php

namespace SIVI\AFDConnectors\Repositories\Contracts;


interface SKPTokenRepository
{

    /**
     * @param string $appKey
     * @param string $username
     * @param string $password
     * @return mixed
     */
    public function getToken($appKey, $username, $password);

    /**
     * @param $token
     * @return mixed
     */
    public function setToken($token);

}