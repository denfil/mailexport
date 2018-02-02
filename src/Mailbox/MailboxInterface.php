<?php

declare(strict_types=1);

namespace MailExport\Mailbox;

interface MailboxInterface
{
    public function open();
    public function close();
    public function getMessageCount();
    public function getMessageOverviewsByNumbers(string $sequence);
    public function getMessageSourceByUid(int $uid);
    public function deleteMessageByUid(int $uid);
    public function getError();
}
