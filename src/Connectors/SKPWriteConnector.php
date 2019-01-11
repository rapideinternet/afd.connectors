<?php

namespace SIVI\AFDConnectors\Connectors;

use SIVI\AFDConnectors\Abstracts\SKP\SKPConnector;
use SIVI\AFDConnectors\Config\Contracts\SKPConfig;
use SIVI\AFDConnectors\Config\Contracts\SKPReadConfig;
use SIVI\AFDConnectors\Config\Contracts\SKPWriteConfig;
use SIVI\AFDConnectors\Enums\SKP\GetFunction;
use SIVI\AFDConnectors\Exceptions\FetchingWSDLFailedException;
use SIVI\AFDConnectors\Interfaces\BatchMessage;
use SIVI\AFDConnectors\Models\SKP\Message;
use SIVI\AFDConnectors\Models\SKP\Message\ProcesInfo;
use SIVI\AFDConnectors\Models\TIME\Envelope\ListEnvelope;
use SIVI\AFDConnectors\Repositories\Contracts\SKPTokenRepository;
use SoapClient;
use SoapParam;
use SoapVar;

class SKPWriteConnector extends SKPConnector implements Contracts\SKPWriteConnector
{
    /**
     * @var SoapClient
     */
    protected $soapClient;
    /**
     * @var SKPWriteConfig
     */
    private $skpWriteConfig;


    public function __construct(SKPTokenRepository $skpTokenRepository, SKPConfig $skpConfig, SKPWriteConfig $skpWriteConfig)
    {
        parent::__construct($skpTokenRepository, $skpConfig);
        $this->skpWriteConfig = $skpWriteConfig;
    }


    /**
     * @param Message $message
     * @return Message
     * @throws FetchingWSDLFailedException
     */
    public function postMessage(Message $message)
    {
        $client = $this->getClient();

        $message->inhoud->gimData = new SoapVar("<ns1:gimData><![CDATA[".$message->inhoud->gimData."]]></ns1:gimData>", XSD_ANYXML, 'test1', 'test2', 'test3', 'test4');

        $parameters = [
            'doeFunctieVraag' => $message
        ];

        $envelope = $client->doeFunctie($parameters);

        return $envelope;
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
     * @return SoapClient
     * @throws FetchingWSDLFailedException
     */
    protected function getClient()
    {
        if ($this->soapClient !== null) {
            return $this->soapClient;
        }

        return $this->soapClient = new SoapClient($this->skpWriteConfig->getWSDL(), [
            'trace' => true,
            'location' => $this->getLocation(),
            'soap_version' => SOAP_1_2,
            'classmap' => [
                'geefResultatenOverzichtResponseGeefResultatenOverzichtAntwoord' => Message::class,
                'berichtType' => Message::class,
                'procesInfoType' => Message\ProcesInfo::class,
                'functieType' => Message\Functie::class,
                'inhoudType' => Message\Content::class,
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
            $this->skpWriteConfig->getAppKey(),
            $this->skpWriteConfig->getUsername(),
            $this->skpWriteConfig->getPassword()
        );

        return vsprintf(
            '%s?oid=%s&goid=%s&apk=%s&tkn=%s',
            [
                $this->skpWriteConfig->getURI(),
                $this->getOwnerId(),
                $this->getGIMObjectId(),
                $this->skpWriteConfig->getAppKey(),
                $token->token,
            ]
        );
    }

}