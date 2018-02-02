<?php

declare(strict_types=1);

namespace MailExport\Pipe;

use MailExport\Mailbox\MailboxInterface;
use MailExport\Map;
use MailExport\Storage\StorageInterface;
use Psr\Log\LoggerInterface;

class Download extends AbstractPipe
{
    /**
     * @var MailboxInterface
     */
    private $mailbox;

    /**
     * @var StorageInterface
     */
    private $storage;

    public function __construct(MailboxInterface $mailbox, StorageInterface $storage, LoggerInterface $logger)
    {
        parent::__construct($logger);
        $this->mailbox = $mailbox;
        $this->storage = $storage;
    }

    public function __invoke(Map $messages): Map
    {
        $total = count($messages);
        if ($total == 0) {
            return $messages;
        }
        $this->logger->debug('Downloading ' . $total . ' messages');
        $current = 0;
        $result = clone $messages;
        foreach ($messages as $uid => $timestamp) {
            $this->logger->debug('Download message ' . ++$current . ' of ' . $total);
            $message = $this->mailbox->getMessageSourceByUid($uid);
            if ($message === false) {
                $this->logger->error('Message with UID ' . $uid . ' not found, but has been selected');
                unset($result[$uid]);
                continue;
            }
            $success = $this->storage->save($uid, $timestamp, $message);
            if ($success === false) {
                $this->logger->error('Failed to save message with UID ' . $uid);
                unset($result[$uid]);
            }
        }
        return $result;
    }
}
