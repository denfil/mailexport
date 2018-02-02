<?php

declare(strict_types=1);

namespace MailExport;

class MessageOverview
{
    private $uid;

    private $date;

    private $received;

    public function __construct(array $overview)
    {
        $this->uid = $overview['uid'];
        $this->date = $overview['date'];
        $this->received = $overview['received'];
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getReceived()
    {
        return $this->received;
    }
}
