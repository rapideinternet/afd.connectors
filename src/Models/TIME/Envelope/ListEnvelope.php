<?php

namespace SIVI\AFDConnectors\Models\TIME\Envelope;

use SIVI\AFDConnectors\Models\TIME\Message;

/**
 * Class MessageList
 * @package SIVI\AFDConnectors\Models\TIME\Envelope
 */
class ListEnvelope
{

    public $getListResult;

    /**
     * @return Message[]
     */
    public function getMessages()
    {
        if (isset($this->getListResult) && isset($this->getListResult->message)) {
            if ($this->getListResult->message instanceof Message) {
                return [$this->getListResult->message];
            }

            return $this->getListResult->message;
        } else {
            return [];
        }
    }
}