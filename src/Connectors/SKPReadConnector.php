<?php

namespace SIVI\AFDConnectors\Connectors;

use SIVI\AFDConnectors\Enums\SKP\GetFunction;
use SIVI\AFDConnectors\Exceptions\FetchingWSDLFailedException;
use SIVI\AFDConnectors\Interfaces\BatchMessage;
use SIVI\AFDConnectors\Models\SKP\Message;
use SIVI\AFDConnectors\Models\SKP\Message\ProcesInfo;
use SIVI\AFDConnectors\Models\TIME\Envelope\ListEnvelope;
use SIVI\AFDConnectors\Models\TIME\Envelope\SingleEnvelope;
use SIVI\AFDConnectors\Models\TIME\Message\Part;
use SIVI\AFDConnectors\Repositories\Contracts\SKPTokenRepository;
use SoapClient;

class SKPReadConnector implements Contracts\SKPReadConnector
{
    /**
     * @var SoapClient
     */
    protected $soapClient;
    /**
     * @var SKPTokenRepository
     */
    private $SKPTokenRepository;

    /**
     * TIMEConnector constructor.
     * @param SKPTokenRepository $SKPTokenRepository
     */
    public function __construct()
    {
        //$this->SKPTokenRepository = $SKPTokenRepository;
    }

    /**
     * @return BatchMessage[]
     * @throws FetchingWSDLFailedException
     */
    public function getMessages()
    {
       return $this->getMessages(GetFunction::ALL_MAIL());
    }

    public function getMessagesByFunction(GetFunction $function)
    {
        $client = $this->getClient();

        $parameters = [
            'geefResultatenOverzichtVraag' => [
                'procesInfo' => [
                    'functie' => [
                        'productId' => 0,
                        'contextId' => substr($function, 0, 3),
                        'functieId' => $function,
                    ]
                ]
            ]
        ];

        /** @var ListEnvelope $listEnvelope */
        $listEnvelope = $client->geefResultatenOverzicht($parameters);

        dd($listEnvelope);

        $messages = [];

        dd($listEnvelope->geefResultatenOverzichtAntwoord->resultatenOverzicht);

        foreach ($listEnvelope->geefResultatenOverzichtAntwoord->resultatenOverzicht as $messageWithoutParts) {
            dd($messageWithoutParts);
            $messages[] = $this->getMessageWithPartsByMessage($messageWithoutParts);
        }

        return $messages;
    }

    /**
     * @return SoapClient
     * @throws FetchingWSDLFailedException
     */
    protected function getClient()
    {
        if ($this->soapClient !== null) {
            return $this->soapClient;
        }

        return $this->soapClient = new SoapClient($this->getWSDL(), [
            'location' => $this->getLocation(),
            'classmap' => [
                'geefResultatenOverzichtResponseGeefResultatenOverzichtAntwoord' => Message::class,
                'procesInfoType' => ProcesInfo::class,
                'berichtType' => Message::class
//                'getMessageResponse' => SingleEnvelope::class,
//                'listMessageOut' => Message::class,
//                'getMessageOut' => Message::class,
//                'partIn_type' => Part::class,
//                'address_type' => Address::class,
            ],
        ]);
    }

    /**
     * @return string
     */
    protected function getWSDL()
    {
        return "https://ezinsure-at.colimbra.net/webservices/ems/emswebservice.asmx?wsdl";
    }

    protected function getLocation()
    {
        //$token = $this->SKPTokenRepository->getToken("","","");

        return "https://ezinsure-at.colimbra.net/webservices/ems/emswebservice.asmx?oid=888&goid=894&apk=hgnt76456sgg4hs1&tkn=2A9532BF3136878D2013C343D604DFCD";
    }

    /**
     * @param Message $message
     * @return Message
     * @throws FetchingWSDLFailedException
     */
    protected function getMessageWithPartsByMessage(Message $message)
    {
        $client = $this->getClient();

        /** @var SingleEnvelope $result */
        $result = $client->getMessage([
            'message' => [
                'listID' => $message->listID
            ]
        ]);

        return $result->getMessage();
    }

    /**
     * @param Message $message
     * @return Part[]
     * @throws FetchingWSDLFailedException
     */
    protected function getMessagePartsByMessage(Message $message)
    {
        return $this->getMessageWithPartsByMessage($message)->getParts();
    }
}