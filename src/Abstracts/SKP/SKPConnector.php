<?php

namespace SIVI\AFDConnectors\Abstracts\SKP;

use SIVI\AFDConnectors\Config\Contracts\SKPConfig;
use SIVI\AFDConnectors\Repositories\Contracts\SKPTokenRepository;

abstract class SKPConnector
{

    /**
     * @var SKPTokenRepository
     */
    protected $skpTokenRepository;
    /**
     * @var SKPConfig
     */
    protected $skpConfig;

    /**
     * SKPConnector constructor.
     * @param SKPTokenRepository $skpTokenRepository
     * @param SKPConfig $skpConfig
     */
    public function __construct(SKPTokenRepository $skpTokenRepository, SKPConfig $skpConfig)
    {
        $this->skpTokenRepository = $skpTokenRepository;
        $this->skpConfig = $skpConfig;
    }

    /**
     * @return string
     */
    protected function getOwnerId()
    {
        return $this->skpConfig->getOwnerId();
    }

    /**
     * @return string
     */
    protected function getGIMObjectId()
    {
        return $this->skpConfig->getGIMObjectId();
    }

    /**
     * @param $appKey
     * @param $username
     * @param $password
     * @return \SIVI\AFDConnectors\Models\SKP\AuthToken
     * @throws \SIVI\AFDConnectors\Exceptions\Exception
     */
    protected function getToken($appKey, $username, $password)
    {
        return $this->skpTokenRepository->getToken($appKey, $username, $password);
    }

}