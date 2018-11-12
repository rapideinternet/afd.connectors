<?php

namespace SIVI\AFDConnectors\Connectors\Contracts;

use SIVI\AFD\Models\Contracts\Message;
use SIVI\AFDConnectors\Enums\TIME\MessageStatus;
use SIVI\AFDConnectors\Exceptions\FetchingWSDLFailedException;
use SIVI\AFDConnectors\Interfaces\BatchMessage;
use SIVI\AFDConnectors\Interfaces\Connector;

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

}