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
     * @return BatchMessage[]
     */
    public function getMessages();

    public function getMessagesByFunction(GetFunction $function);

}