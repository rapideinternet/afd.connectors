<?php

namespace SIVI\AFDConnectors\Connectors\Contracts;

use SIVI\AFD\Models\Contracts\Message;
use SIVI\AFDConnectors\Interfaces\Connector;

interface SKPWriteConnector extends Connector
{

    /**
     * @param Message $message
     */
    public function postMessage(Message $message);

}