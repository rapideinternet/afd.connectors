<?php

namespace SIVI\AFDConnectors\Repositories;

use SIVI\AFDConnectors\Config\Contracts\SKPConfig;
use SIVI\AFDConnectors\Exceptions\Exception;
use SIVI\AFDConnectors\Models\SKP\AuthToken;
use SoapClient;

class SKPTokenRepository implements \SIVI\AFDConnectors\Repositories\Contracts\SKPTokenRepository
{
    /** @var SoapClient */
    protected $soapClient;
    /**
     * @var SKPConfig
     */
    protected $skpConfig;


    public function __construct(SKPConfig $skpConfig)
    {
        $this->skpConfig = $skpConfig;
    }

    /**
     * @return SoapClient
     */
    protected function getClient()
    {
        if ($this->soapClient !== null) {
            return $this->soapClient;
        }

        return $this->soapClient = new SoapClient($this->skpConfig->getAuthWSDL(),[
                'classmap' => [
                    'GetTokenResponse' => AuthToken::class,
                ],
            ]);
    }

    /**
     * @param string $appKey
     * @param string $username
     * @param string $password
     * @return AuthToken
     * @throws Exception
     */
    public function getToken($appKey, $username, $password)
    {
        return $this->getClient()->GetToken([
            'appKey' => $appKey,
            'username' => $username,
            'password' => $password,
        ]);
    }

}