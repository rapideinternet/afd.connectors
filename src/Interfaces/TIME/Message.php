<?php

namespace SIVI\AFDConnectors\Interfaces\TIME;

use SIVI\AFDConnectors\Enums\TIME\MessageStatus;
use SIVI\AFDConnectors\Enums\TIME\MessageType;
use SIVI\AFDConnectors\Interfaces\BatchMessage;
use SIVI\AFDConnectors\Models\TIME\Message\Address;
use SIVI\AFDConnectors\Models\TIME\Message\Part;

/**
 * Interface Message
 * @package SIVI\AFDConnectors\Interfaces\TIME
 */
interface Message extends BatchMessage
{

    /**
     * @return Part[]
     */
    public function getParts();

    /**
     * @return Address
     */
    public function getFrom(): Address;

    /**
     * @param Address $from
     */
    public function setFrom(Address $from): void;

    /**
     * @return Address
     */
    public function getTo(): Address;

    /**
     * @param Address $to
     */
    public function setTo(Address $to): void;

    /**
     * @return null|string
     */
    public function getSubject(): ?string;

    /**
     * @param null|string $subject
     */
    public function setSubject(?string $subject): void;

    /**
     * @return null|string
     */
    public function getSent(): ?string;

    /**
     * @param null|string $sent
     */
    public function setSent(?string $sent): void;

    /**
     * @return null|string
     */
    public function getListID(): ?string;

    /**
     * @param null|string $listID
     */
    public function setListID(?string $listID): void;

    /**
     * @return null|string
     */
    public function getMessageID(): ?string;

    /**
     * @param null|string $messageID
     */
    public function setMessageID(?string $messageID): void;

    /**
     * @return null|MessageStatus
     */
    public function getStatus(): ?MessageStatus;

    /**
     * @param null|string|MessageStatus $status
     */
    public function setStatus($status): void;

    /**
     * @return null|string
     */
    public function getMessageType(): ?string;

    /**
     * @param null|string $messageType
     */
    public function setMessageType(?string $messageType): void;

    /**
     * @return null|string
     */
    public function getMessageSize(): ?string;

    /**
     * @param null|string $messageSize
     */
    public function setMessageSize(?string $messageSize): void;

}