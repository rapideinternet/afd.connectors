<?php

namespace SIVI\AFDConnectors\Models\TIME\Envelope;

use SIVI\AFDConnectors\Models\TIME\Message;

/**
 * Class MessageList
 * @package SIVI\AFDConnectors\Models\TIME\Envelope
 */
class SingleEnvelope
{

    public $getMessageResult;

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->getMessageResult;
    }
}