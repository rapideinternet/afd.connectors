<?php

namespace SIVI\AFDConnectors\Interfaces;

/**
 * Interface Message
 * @package SIVI\AFDConnectors\Interfaces
 */
interface BatchMessage
{

    /**
     * @return Message[]
     */
    public function getMessages();

}