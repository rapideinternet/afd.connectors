<?php

namespace SIVI\AFDConnectors\Abstracts;

use SoapClient;

abstract class SKPConnector
{

    /** @var SoapClient */
    protected $soapClient;

    public function __construct()
    {
    }

    /**
     * @return SoapClient
     */
    protected function getTokenClient()
    {
        if ($this->soapClient !== null) {
            return $this->soapClient;
        }

        return $this->soapClient = new SoapClient(
            'https://ezinsure-at.colimbra.net/webservices/usermanagement/tokenwebservice.asmx?wsdl',
            [
            'classmap' => [
                'getListResponse' => ListEnvelope::class,
                'getMessageResponse' => SingleEnvelope::class,
                'listMessageOut' => Message::class,
                'getMessageOut' => Message::class,
                'partIn_type' => Part::class,
                'address_type' => Address::class,
            ],
        ]);
    }

}