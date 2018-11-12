<?php

namespace SIVI\AFDConnectors\Config\Contracts;

interface SKPReadConfig
{

    /**
     * The uri of the WSDL
     *
     * @return string
     */
    public function getWSDL();

    /**
     * The app key
     *
     * @return string
     */
    public function getAppKey();

    /**
     * The username
     *
     * @return string
     */
    public function getUsername();

    /**
     * The password
     *
     * @return string
     */
    public function getPassword();

}