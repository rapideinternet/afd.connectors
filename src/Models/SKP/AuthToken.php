<?php

namespace SIVI\AFDConnectors\Models\SKP;

use SIVI\AFDConnectors\Exceptions\Exception;

/**
 * Class AuthToken
 * @package SIVI\AFDConnectors\Models\SKP
 */
class AuthToken
{

    public $token;

    public function __set($name, $value)
    {
        if ($name === 'GetTokenResult' && $value->Category === 'Error') {
            throw new Exception($value->Description);
        }

        $this->{$name} = $value;
    }
}