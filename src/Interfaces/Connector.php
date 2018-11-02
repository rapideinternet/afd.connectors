<?php

namespace SIVI\AFDConnectors\Interfaces;

interface Connector
{

    /**
     * @return BatchMessage[]
     */
    public function getMessages();

}