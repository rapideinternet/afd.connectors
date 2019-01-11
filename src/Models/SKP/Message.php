<?php

namespace SIVI\AFDConnectors\Models\SKP;

use SIVI\AFDConnectors\Models\SKP\Message\Content;
use SIVI\AFDConnectors\Models\SKP\Message\Functie;
use SIVI\AFDConnectors\Models\SKP\Message\ProcesInfo;

/**
 * Class Message
 */
class Message
{
    /**
     * @var ProcesInfo|null
     */
    public $procesInfo;

    /**
     * @var Message[]
     */
    public $resultatenOverzicht;

    /**
     * @var Content
     */
    public $inhoud;

    public function __construct()
    {
        $this->procesInfo = new ProcesInfo();
        $this->procesInfo->functie = new Functie();
        $this->inhoud = new Content();
    }


    public function getMessages()
    {
        if (!isset($this->resultatenOverzicht)){
            return [];
        }

        if ($this->resultatenOverzicht->item instanceof Message) {
            return [$this->resultatenOverzicht->item];
        }

        return $this->resultatenOverzicht->item;
    }

}