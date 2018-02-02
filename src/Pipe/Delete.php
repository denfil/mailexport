<?php

declare(strict_types=1);

namespace MailExport\Pipe;

use MailExport\Mailbox\MailboxInterface;
use MailExport\Map;
use Psr\Log\LoggerInterface;

class Delete extends AbstractPipe
{
    /**
     * @var MailboxInterface
     */
    private $mailbox;

    public function __construct(MailboxInterface $mailbox, LoggerInterface $logger)
    {
        parent::__construct($logger);
        $this->mailbox = $mailbox;
    }

    public function __invoke(Map $messages): Map
    {
        $total = count($messages);
        if ($total == 0) {
            return $messages;
        }
        $this->logger->debug('Deleting ' . $total . ' messages');
        $current = 0;
        foreach ($messages->keys() as $uid) {
            $this->logger->debug('Delete message ' . ++$current . ' of ' .  $total);
            $this->mailbox->deleteMessageByUid($uid);
        }
        return $messages;
    }
}
