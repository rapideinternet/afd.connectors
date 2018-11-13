<?php

namespace SIVI\AFDConnectors\Connectors\Contracts;

use SIVI\AFD\Models\Contracts\Message;
use SIVI\AFDConnectors\Enums\SKP\GetFunction;
use SIVI\AFDConnectors\Enums\TIME\MessageStatus;
use SIVI\AFDConnectors\Exceptions\FetchingWSDLFailedException;
use SIVI\AFDConnectors\Interfaces\BatchMessage;
use SIVI\AFDConnectors\Interfaces\Connector;

interface SKPReadConnector extends Connector
{

    /**
     * @return Message[]
     */
    public function getMessages();

    /**
     * @param GetFunction $function
     * @return mixed
     */
    public function getMessagesByFunction(GetFunction $function);

    /**
     * @param $id
     * @return Message|null
     */
    public function getMessageById($id);

    /**
     * @param Message[] $messages
     */
    public function ackMessages($messages);

}