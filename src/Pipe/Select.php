<?php

declare(strict_types=1);

namespace MailExport\Pipe;

use MailExport\Mailbox\MailboxInterface;
use MailExport\Map;
use MailExport\Period;
use Psr\Log\LoggerInterface;

class Select extends AbstractPipe
{
    /**
     * @var MailboxInterface
     */
    private $mailbox;

    /**
     * @var Period
     */
    private $period;

    const CHUNK_SIZE = 1000;

    public function __construct(MailboxInterface $mailbox, Period $period, LoggerInterface $logger)
    {
        parent::__construct($logger);
        $this->mailbox = $mailbox;
        $this->period = $period;
    }

    public function __invoke(Map $messages): Map
    {
        $messages->clear();
        $this->logger->debug('Counting messages in mailbox');
        $total = $this->mailbox->getMessageCount();
        if ($total === false) {
            $this->logger->error($this->mailbox->getError());
            return $messages;
        }
        $this->logger->debug($total . ' messages found');
        if ($total < 1) {
            return $messages;
        }
        $from = 1;
        while ($from < $total) {
            $to = min($total, $from + static::CHUNK_SIZE - 1);
            $messages = $this->handle($messages, $from, $to);
            $from += static::CHUNK_SIZE;
        }
        return $messages;
    }

    private function handle(Map $messages, int $from, int $to): Map
    {
        $this->logger->debug('Getting overview of messages ' . $from . '-' . $to);
        $overviews = $this->mailbox->getMessageOverviewsByNumbers($from . ':' . $to);
        if ($overviews === false) {
            throw new \RuntimeException($this->mailbox->getError());
        }
        foreach ($overviews as $overview) {
            $timestamp = $this->getTimestamp($overview);
            $matched = $this->period->contains($timestamp);
            if ($matched) {
                $messages[$overview['uid']] = $timestamp;
            }
        }
        return $messages;
    }

    private function getTimestamp(array $messageOverview): int
    {
        $timestamp = strtotime($messageOverview['date']);
        if ($timestamp === false) {
            $errorMsg = 'Failed to parse Date header "' . $messageOverview['date']
                . '" of message with UID ' . $messageOverview['uid'];
            $this->logger->error($errorMsg);
            $timestamp = (int)$messageOverview['received'];
        }
        return $timestamp;
    }
}
