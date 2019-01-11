<?php

namespace SIVI\AFDConnectors\Connectors\Contracts;

use SIVI\AFDConnectors\Enums\TIME\MessageStatus;
use SIVI\AFDConnectors\Exceptions\FetchingWSDLFailedException;
use SIVI\AFDConnectors\Interfaces\BatchMessage;
use SIVI\AFDConnectors\Interfaces\Connector;
use SIVI\AFDConnectors\Interfaces\TIME\Message;

interface TIMEConnector extends Connector
{

    /**
     * @return BatchMessage[]
     */
    public function getMessages();

    /**
     * @param MessageStatus $messageStatus
     * @return BatchMessage[]
     * @throws FetchingWSDLFailedException
     */
    public function getMessagesByStatus(MessageStatus $messageStatus);

    /**
     * @param Message $batchMessage
     * @return bool
     */
    public function ackMessage(Message $batchMessage);

}