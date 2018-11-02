<?php

namespace SIVI\AFDConnectors\Models\TIME;

use SIVI\AFDConnectors\Enums\TIME\MessageStatus;
use SIVI\AFDConnectors\Models\TIME\Message\Address;
use SIVI\AFDConnectors\Models\TIME\Message\Part;

/**
 * Class Message
 * @package SIVI\AFDConnectors\Models\TIME
 */
class Message implements \SIVI\AFDConnectors\Interfaces\TIME\Message
{

    /**
     * @var Address
     */
    public $from;
    /**
     * @var Address
     */
    public $to;
    /**
     * @var string|null
     */
    public $subject;
    /**
     * @var string|null
     */
    public $sent;
    /**
     * @var string|null
     */
    public $listID;
    /**
     * @var string|null
     */
    public $messageID;
    /**
     * @var string|null
     */
    public $status;
    /**
     * @var string|null
     */
    public $messageType;
    /**
     * @var string|null
     */
    public $messageSize;
    /**
     * @var Part|Part[]
     */
    public $part;


    /**
     * @return \SIVI\AFDConnectors\Interfaces\Message[]
     */
    public function getMessages()
    {
        return $this->getParts();
    }

    /**
     * @return Part[]
     */
    public function getParts()
    {
        if ($this->part instanceof Part) {
            return [$this->part];
        }

        return $this->part;
    }

    /**
     * @return Address
     */
    public function getFrom(): Address
    {
        return $this->from;
    }

    /**
     * @param Address $from
     */
    public function setFrom(Address $from): void
    {
        $this->from = $from;
    }

    /**
     * @return Address
     */
    public function getTo(): Address
    {
        return $this->to;
    }

    /**
     * @param Address $to
     */
    public function setTo(Address $to): void
    {
        $this->to = $to;
    }

    /**
     * @return null|string
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param null|string $subject
     */
    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return null|string
     */
    public function getSent(): ?string
    {
        return $this->sent;
    }

    /**
     * @param null|string $sent
     */
    public function setSent(?string $sent): void
    {
        $this->sent = $sent;
    }

    /**
     * @return null|string
     */
    public function getListID(): ?string
    {
        return $this->listID;
    }

    /**
     * @param null|string $listID
     */
    public function setListID(?string $listID): void
    {
        $this->listID = $listID;
    }

    /**
     * @return null|string
     */
    public function getMessageID(): ?string
    {
        return $this->messageID;
    }

    /**
     * @param null|string $messageID
     */
    public function setMessageID(?string $messageID): void
    {
        $this->messageID = $messageID;
    }

    /**
     * @return null|MessageStatus
     */
    public function getStatus(): ?MessageStatus
    {
        return new MessageStatus($this->status);
    }

    /**
     * @param null|string|MessageStatus $status
     */
    public function setStatus($status): void
    {
        if (MessageStatus::isValid($status)) {
            $this->status = new MessageStatus($status);
        } else {
            $this->status = $status;
        }
    }

    /**
     * @return null|string
     */
    public function getMessageType(): ?string
    {
        return $this->messageType;
    }

    /**
     * @param null|string $messageType
     */
    public function setMessageType(?string $messageType): void
    {
        $this->messageType = $messageType;
    }

    /**
     * @return null|string
     */
    public function getMessageSize(): ?string
    {
        return $this->messageSize;
    }

    /**
     * @param null|string $messageSize
     */
    public function setMessageSize(?string $messageSize): void
    {
        $this->messageSize = $messageSize;
    }

}