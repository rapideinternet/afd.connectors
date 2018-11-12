<?php

namespace SIVI\AFDConnectors\Config\Contracts;

interface SKPConfig
{

    /**
     * @return string
     */
    public function getAuthWSDL();

    /**
     * @return string
     */
    public function getOwnerId();

    /**
     * @return string
     */
    public function getGIMObjectId();

}