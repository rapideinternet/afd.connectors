<?php

namespace SIVI\AFDConnectors\Models\TIME\Message;

use SIVI\AFDConnectors\Exceptions\ContentDecodeFailedException;
use SIVI\AFDConnectors\Interfaces\TIME\MessagePart;

/**
 * Class Part
 * @package SIVI\AFDConnectors\Models\TIME\Message
 */
class Part implements MessagePart
{

    public $extension;
    public $data;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->extension;
    }

    /**
     * @return string
     * @throws ContentDecodeFailedException
     */
    public function getData()
    {
        if (($decoded = base64_decode($this->data)) === false) {
            throw new ContentDecodeFailedException('The content of this message could not be decoded via base64');
        }

        return $decoded;
    }
}