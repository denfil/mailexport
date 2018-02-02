<?php

declare(strict_types=1);

namespace MailExport\Pipe;

use MailExport\Mailbox\MailboxInterface;
use MailExport\Map;
use Psr\Log\LoggerInterface;

class Disconnect extends AbstractPipe
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
        $this->logger->debug('Closing connection with mailbox');
        $this->mailbox->close();
        return $messages;
    }
}
