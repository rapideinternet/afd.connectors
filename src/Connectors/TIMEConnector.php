<?php

namespace SIVI\AFDConnectors\Connectors;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SIVI\AFDConnectors\Config\Contracts\TIMEConfig;
use SIVI\AFDConnectors\Enums\TIME\MessageStatus;
use SIVI\AFDConnectors\Exceptions\FetchingWSDLFailedException;
use SIVI\AFDConnectors\Interfaces\BatchMessage;
use SIVI\AFDConnectors\Models\TIME\Envelope\ListEnvelope;
use SIVI\AFDConnectors\Models\TIME\Envelope\SingleEnvelope;
use SIVI\AFDConnectors\Models\TIME\Message;
use SIVI\AFDConnectors\Models\TIME\Message\Address;
use SIVI\AFDConnectors\Models\TIME\Message\Part;
use SoapClient;

class TIMEConnector implements Contracts\TIMEConnector
{
    /**
     * @var SoapClient
     */
    protected $soapClient;
    /**
     * @var TIMEConfig
     */
    protected $config;

    /**
     * TIMEConnector constructor.
     * @param TIMEConfig $config
     */
    public function __construct(TIMEConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return BatchMessage[]
     * @throws FetchingWSDLFailedException
     */
    public function getMessages()
    {
        return $this->getMessagesByStatus(MessageStatus::UNREAD());
    }

    /**
     * @param MessageStatus $messageStatus
     * @return BatchMessage[]
     * @throws FetchingWSDLFailedException
     */
    public function getMessagesByStatus(MessageStatus $messageStatus)
    {
        $client = $this->getClient();

        $parameters = [
            'list' => [
                'selection' => [
                    'start' => Carbon::now()->subMonths(5)->format('Y-m-d'),
                    'status' => $messageStatus->getValue(),
                ]
            ]
        ];

        /** @var ListEnvelope $listEnvelope */
        $listEnvelope = $client->getList($parameters);

        $messages = [];

        foreach ($listEnvelope->getMessages() as $messageWithoutParts) {
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
            'proxy_host' => $this->config->getHost(),
            'local_cert' => $this->config->getCertificatePath(),
            'passphrase' => $this->config->getCertificatePassphrase(),
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

    /**
     * @return string
     * @throws FetchingWSDLFailedException
     */
    protected function getWSDL()
    {
        $client = new Client();

        try {
            $response = $client->request('GET', sprintf('%s?wsdl', $this->config->getHost()),
                ['cert' => [$this->config->getCertificatePath(), $this->config->getCertificatePassphrase()]]);

            $path = sprintf('%s/stsPort.wsdl', $this->config->getWSDLStoragePath());

            file_put_contents($path, $response->getBody()->getContents());

            return $path;
        } catch (GuzzleException $exception) {
            throw new FetchingWSDLFailedException('Could not fetch WSDL', 0, $exception);
        }
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