<?php

namespace SIVI\AFDConnectors\Connectors;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use SIVI\AFDConnectors\Config\Contracts\TIMEConfig;
use SIVI\AFDConnectors\Enums\TIME\MessageStatus;
use SIVI\AFDConnectors\Exceptions\CertificateExpiredException;
use SIVI\AFDConnectors\Exceptions\CertificateInvalidException;
use SIVI\AFDConnectors\Exceptions\FetchingWSDLFailedException;
use SIVI\AFDConnectors\Exceptions\FileNotFoundException;
use SIVI\AFDConnectors\Exceptions\WritingWSDLFailedException;
use SIVI\AFDConnectors\Interfaces\BatchMessage;
use SIVI\AFDConnectors\Interfaces\TIME\Message;
use SIVI\AFDConnectors\Models\TIME\Envelope\ListEnvelope;
use SIVI\AFDConnectors\Models\TIME\Envelope\SingleEnvelope;
use SIVI\AFDConnectors\Models\TIME\Message\Address;
use SIVI\AFDConnectors\Models\TIME\Message\Part;
use SIVI\AFDConnectors\Repositories\Contracts\WSDLCacheRepository;
use SoapClient;

class TIMEConnector implements Contracts\TIMEConnector
{
    static $CACHE_KEY = 'afd_connector_time_wsdl_cache';

    /**
     * @var SoapClient
     */
    protected $soapClient;
    /**
     * @var TIMEConfig
     */
    protected $config;

    /**
     * @var WSDLCacheRepository
     */
    protected $wsdlCacheRepository;

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
     * @throws CertificateExpiredException
     * @throws CertificateInvalidException
     * @throws FetchingWSDLFailedException
     * @throws FileNotFoundException
     */
    public function getMessages()
    {
        return $this->getMessagesByStatus(MessageStatus::UNREAD());
    }

    /**
     * @param MessageStatus $messageStatus
     * @return BatchMessage[]
     * @throws CertificateExpiredException
     * @throws CertificateInvalidException
     * @throws FetchingWSDLFailedException
     * @throws FileNotFoundException
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
     * @throws CertificateExpiredException
     * @throws CertificateInvalidException
     * @throws FetchingWSDLFailedException
     * @throws FileNotFoundException
     */
    protected function getClient()
    {
        if ($this->soapClient !== null) {
            return $this->soapClient;
        }

        $this->validateCertificate();

        return $this->soapClient = new SoapClient($this->getWSDL(), [
            'proxy_host' => $this->config->getHost(),
            'local_cert' => $this->config->getCertificatePath(),
            'passphrase' => $this->config->getCertificatePassphrase(),
            'classmap' => [
                'getListResponse' => ListEnvelope::class,
                'getMessageResponse' => SingleEnvelope::class,
                'listMessageOut' => \SIVI\AFDConnectors\Models\TIME\Message::class,
                'getMessageOut' => \SIVI\AFDConnectors\Models\TIME\Message::class,
                'partIn_type' => Part::class,
                'address_type' => Address::class,
            ],
        ]);
    }

    /**
     * @return void
     * @throws CertificateExpiredException
     * @throws CertificateInvalidException
     * @throws FileNotFoundException
     */
    protected function validateCertificate()
    {
        if (!file_exists($this->config->getCertificatePath())) {
            throw new FileNotFoundException(sprintf(
                'Could not find certificate at "%s" or permissions are incorrect',
                $this->config->getCertificatePath()
            ));
        }

        $certData = file_get_contents($this->config->getCertificatePath());

        try {
            $certInfo = openssl_x509_parse($certData);
        } catch (\Exception $exception) {
            throw new CertificateInvalidException('Could not parse certificate', 0, $exception);
        }

        $validTo = Carbon::createFromTimestamp($certInfo['validTo_time_t']);

        if ($validTo->lt(Carbon::now())) {
            throw new CertificateExpiredException(sprintf(
                'The certificate is no longer valid and expired on "%s"',
                $validTo->toDateTimeString()
            ));
        }
    }

    /**
     * @return string
     * @throws FetchingWSDLFailedException
     * @throws WritingWSDLFailedException
     */
    protected function getWSDL()
    {
        $client = new Client();

        try {
            if ($this->hasCachedWSDLResponse()) {
                $wsdlContent = $this->getCachedWSDLResponse();
            } else {
                $response = $client->request('GET', sprintf('%s?wsdl', $this->config->getHost()),
                    ['cert' => [$this->config->getCertificatePath(), $this->config->getCertificatePassphrase()]]);
                $wsdlContent = $response->getBody()->getContents();

                $this->cacheWSDLResponse($wsdlContent);
            }

            return $this->storeWSDLContentOnFileSystem($wsdlContent);
        } catch (GuzzleException $exception) {
            throw new FetchingWSDLFailedException('Could not fetch WSDL', 0, $exception);
        }
    }

    /**
     * @return bool
     */
    protected function hasCachedWSDLResponse(): bool
    {
        return $this->wsdlCacheRepository !== null
            && $this->wsdlCacheRepository->has(self::$CACHE_KEY);
    }

    /**
     * @return string|null
     */
    protected function getCachedWSDLResponse(): ?string
    {
        return $this->wsdlCacheRepository->get('afd_connector_time_wsdl_cache');
    }

    /**
     * @param string $wsdlContent
     */
    protected function cacheWSDLResponse(string $wsdlContent): void
    {
        if ($this->wsdlCacheRepository !== null) {
            $this->wsdlCacheRepository->add('afd_connector_time_wsdl_cache', $wsdlContent, Carbon::now()->addDay());
        }
    }

    /**
     * @param string $wsdlContent
     * @return string
     * @throws WritingWSDLFailedException
     */
    protected function storeWSDLContentOnFileSystem(string $wsdlContent): string
    {
        @mkdir($this->config->getWSDLStoragePath(), 0755, true);
        $path = sprintf('%s/stsPort.wsdl', $this->config->getWSDLStoragePath());

        if (file_put_contents($path, $wsdlContent) === false) {
            throw new WritingWSDLFailedException('Could not write temporary wsdl.');
        }

        return $path;
    }

    /**
     * @param \SIVI\AFDConnectors\Models\TIME\Message $message
     * @return \SIVI\AFDConnectors\Models\TIME\Message
     * @throws CertificateExpiredException
     * @throws CertificateInvalidException
     * @throws FetchingWSDLFailedException
     * @throws FileNotFoundException
     */
    protected function getMessageWithPartsByMessage(\SIVI\AFDConnectors\Models\TIME\Message $message)
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
     * @return bool
     * @throws CertificateExpiredException
     * @throws CertificateInvalidException
     * @throws FetchingWSDLFailedException
     * @throws FileNotFoundException
     */
    public function ackMessage(Message $message)
    {
        $client = $this->getClient();

        $parameters = [
            'message' => [
                'listID' => $message->getListID()
            ]
        ];

        $ackResult = $client->ackMessage($parameters);

        return $ackResult->ackMessageResult->resultCode === "000";
    }

    /**
     * @param WSDLCacheRepository $wsdlCacheRepository
     */
    public function setWSDLCacheRepository(WSDLCacheRepository $wsdlCacheRepository): void
    {
        $this->wsdlCacheRepository = $wsdlCacheRepository;
    }

    /**
     * @param \SIVI\AFDConnectors\Models\TIME\Message $message
     * @return Part[]
     * @throws CertificateExpiredException
     * @throws CertificateInvalidException
     * @throws FetchingWSDLFailedException
     * @throws FileNotFoundException
     */
    protected function getMessagePartsByMessage(\SIVI\AFDConnectors\Models\TIME\Message $message)
    {
        return $this->getMessageWithPartsByMessage($message)->getParts();
    }
}