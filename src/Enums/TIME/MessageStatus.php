<?php namespace SIVI\AFDConnectors\Enums\TIME;

use MyCLabs\Enum\Enum;

/**
 * Class MessageStatus
 * @package SIVI\AFDConnectors\Enums\TIME
 *
 * @method static MessageStatus UNREAD()
 * @method static MessageStatus READ()
 */
class MessageStatus extends Enum
{
    const UNREAD = 'U';
    const READ = 'R';
}
