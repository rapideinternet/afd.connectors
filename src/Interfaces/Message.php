<?php

namespace SIVI\AFDConnectors\Interfaces;

/**
 * Interface Message
 * @package SIVI\AFDConnectors\Interfaces
 */
interface Message
{

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getData();

}