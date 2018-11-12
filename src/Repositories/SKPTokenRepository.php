<?php

namespace SIVI\AFDConnectors\Repositories;

use SIVI\AFDConnectors\Models\SKP\AuthToken;
use SoapClient;

class SKPTokenRepository implements \SIVI\AFDConnectors\Repositories\Contracts\SKPTokenRepository
{
    /** @var SoapClient */
    protected $soapClient;

    /**
     * @return SoapClient
     */
    protected function getClient()
    {
        if ($this->soapClient !== null) {
            return $this->soapClient;
        }

        return $this->soapClient = new SoapClient(
            'https://ezinsure-at.colimbra.net/webservices/usermanagement/tokenwebservice.asmx?wsdl',
            [
                'classmap' => [
                    'GetTokenResponse' => AuthToken::class,
                ],
            ]);
    }

    /**
     * @param string $appKey
     * @param string $username
     * @param string $password
     * @return mixed
     */
    public function getToken($appKey, $username, $password)
    {
        dd($this->getClient()->GetToken([
            'appKey' => $appKey,
            'username' => $username,
            'password' => $password,
        ]));
    }

    /**
     * @param $token
     * @return mixed
     */
    public function setToken($token)
    {

    }

}