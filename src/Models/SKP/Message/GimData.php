<?php

namespace SIVI\AFDConnectors\Models\SKP\Message;

class GimData
{

    public $rawGimData;

    public function __toString()
    {
        return $this->rawGimData;
    }

}