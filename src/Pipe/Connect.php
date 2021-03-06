<?php

declare(strict_types=1);

namespace MailExport\Pipe;

use MailExport\Mailbox\MailboxInterface;
use MailExport\Map;
use Psr\Log\LoggerInterface;

class Connect extends AbstractPipe
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
        $this->logger->debug('Opening connection with mailbox');
        $this->mailbox->open();
        return $messages;
    }
}
