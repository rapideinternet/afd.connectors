<?php

namespace SIVI\AFDConnectors\Connectors\Contracts;

use SIVI\AFD\Models\Contracts\Message;
use SIVI\AFDConnectors\Enums\TIME\MessageStatus;
use SIVI\AFDConnectors\Exceptions\FetchingWSDLFailedException;
use SIVI\AFDConnectors\Interfaces\Connector;

interface TIMEConnector extends Connector
{

    /**
     * @return \SIVI\AFDConnectors\Interfaces\TIME\Message[]
     */
    public function getMessages();

    /**
     * @param MessageStatus $messageStatus
     * @return Message[]
     * @throws FetchingWSDLFailedException
     */
    public function getMessagesByStatus(MessageStatus $messageStatus);

}