<?php

namespace SIVI\AFDConnectors\Connectors;

use SIVI\AFDConnectors\Abstracts\SKP\SKPConnector;
use SIVI\AFDConnectors\Config\Contracts\SKPConfig;
use SIVI\AFDConnectors\Config\Contracts\SKPReadConfig;
use SIVI\AFDConnectors\Enums\SKP\GetFunction;
use SIVI\AFDConnectors\Exceptions\FetchingWSDLFailedException;
use SIVI\AFDConnectors\Interfaces\BatchMessage;
use SIVI\AFDConnectors\Models\SKP\Envelope;
use SIVI\AFDConnectors\Models\SKP\Message;
use SIVI\AFDConnectors\Models\SKP\Message\ProcesInfo;
use SIVI\AFDConnectors\Models\TIME\Envelope\ListEnvelope;
use SIVI\AFDConnectors\Repositories\Contracts\SKPTokenRepository;
use SoapClient;

class SKPReadConnector extends SKPConnector implements Contracts\SKPReadConnector
{
    /**
     * @var SoapClient
     */
    protected $soapClient;
    /**
     * @var SKPReadConfig
     */
    private $skpReadConfig;

    public function __construct(SKPTokenRepository $skpTokenRepository, SKPConfig $skpConfig, SKPReadConfig $skpReadConfig)
    {
        parent::__construct($skpTokenRepository, $skpConfig);
        $this->skpReadConfig = $skpReadConfig;
    }

    /**
     * @return BatchMessage[]
     * @throws FetchingWSDLFailedException
     */
    public function getMessages()
    {
        return $this->getMessagesByFunction(GetFunction::ALL_MAIL());
    }

    /**
     * @param GetFunction $function
     * @return mixed
     * @throws FetchingWSDLFailedException
     */
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

        $listEnvelope = $client->geefResultatenOverzicht($parameters);

        $parameters = [
            'geefResultatenVraag' => [
                'procesInfo' => [
                    'functie' => [
                        'productId' => 0,
                        'contextId' => 701,
                        'functieId' => 7801,
                    ]
                ],
                'resultatenOverzicht' => $listEnvelope->geefResultatenOverzichtAntwoord->getMessages()
            ]
        ];

        $messagesEnvelope = $client->geefResultaten($parameters);

        return $messagesEnvelope->geefResultatenAntwoord->getMessages();
    }

    public function getMessageById($id)
    {
        $client = $this->getClient();

        $parameters = [
            'geefResultatenVraag' => [
                'procesInfo' => [
                    'functie' => [
                        'productId' => 0,
                        'contextId' => 701,
                        'functieId' => 7801,
                    ]
                ],
                'resultatenOverzicht' => [
                    'item' => [
                        'procesInfo' => [
                            'procesId' => $id
                        ]
                    ]
                ]
            ]
        ];

        $messages = $client->geefResultaten($parameters)->geefResultatenAntwoord->getMessages();

        return $messages == [] ? null : $messages[0];
    }

    public function ackMessages($messages)
    {
        $client = $this->getClient();

        $parameters = [
            'ontvangstBevestigingVraag' => [
                'procesInfo' => [
                    'functie' => [
                        'productId' => 0,
                        'contextId' => 702,
                        'functieId' => 7900,
                    ]
                ],
                'resultatenOverzicht' => $messages
            ]
        ];

        $client->ontvangstBevestiging($parameters);
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

        return $this->soapClient = new SoapClient($this->skpReadConfig->getWSDL(), [
            'location' => $this->getLocation(),
            'classmap' => [
                'geefResultatenOverzichtResponseGeefResultatenOverzichtAntwoord' => Envelope::class,
                'geefResultatenResponseGeefResultatenAntwoord' => Envelope::class,
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
     * @throws \SIVI\AFDConnectors\Exceptions\Exception
     */
    protected function getLocation()
    {
        $token = $this->getToken(
            $this->skpReadConfig->getAppKey(),
            $this->skpReadConfig->getUsername(),
            $this->skpReadConfig->getPassword()
        );

        return vsprintf('%s?oid=%s&goid=%s&apk=%s&tkn=%s',[
            $this->skpReadConfig->getURI(),
            $this->getOwnerId(),
            $this->getGIMObjectId(),
            $this->skpReadConfig->getAppKey(),
            $token->token,
        ]);
    }

}