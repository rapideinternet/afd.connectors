<?php

namespace SIVI\AFDConnectors\Models\SKP;

use SIVI\AFDConnectors\Models\SKP\Message\ProcesInfo;

/**
 * Class Message
 * @package SIVI\AFDConnectors\Models\TIME
 */
class Message
{
    /**
     * @var ProcesInfo|null
     */
    public $procesInfo;

    /**
     * @var Message|Message[]
     */
    public $resultatenoverzicht;


    public function getMessages()
    {
        return $this->getParts();
    }

    /**
     * @return Message[]
     */
    public function getParts()
    {
        if ($this->resultatenoverzicht instanceof Message) {
            return [$this->resultatenoverzicht];
        }

        return $this->resultatenoverzicht;
    }

}