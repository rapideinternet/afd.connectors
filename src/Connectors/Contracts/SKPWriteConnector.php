<?php

namespace SIVI\AFDConnectors\Connectors\Contracts;

use SIVI\AFDConnectors\Interfaces\Connector;
use SIVI\AFDConnectors\Models\SKP\Message;

interface SKPWriteConnector extends Connector
{

    /**
     * @param Message $message
     * @return Message
     */
    public function postMessage(Message $message);

}