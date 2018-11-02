<?php

namespace SIVI\AFDConnectors\Config\Contracts;

interface TIMEConfig
{

    /**
     * Get the host of the service
     *
     * @return string
     */
    public function getHost();

    /**
     * The path where the WSDL for the service will be stored.
     *
     * This is because the WSDL is not public and needs to be requested with
     * a client certificate. The PHP SOAP extension does not support
     * this and that is why a local one must be created.
     *
     * @return string
     */
    public function getWSDLStoragePath();

    /**
     * The path where the Solera certificate is stored.
     * This needs to be a PEM file.
     *
     * @return string
     */
    public function getCertificatePath();

    /**
     * The passphrase of the Solera certificate.
     *
     * @return string
     */
    public function getCertificatePassphrase();

}