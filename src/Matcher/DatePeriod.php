<?php

declare(strict_types=1);

namespace MailExport\Matcher;

use Psr\Log\LoggerInterface;

class DatePeriod implements MatcherInterface
{
    /**
     * @var int
     */
    private $from;

    /**
     * @var int
     */
    private $to;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(int $from, int $to, LoggerInterface $logger)
    {
        $valid = $this->validate($from, $to);
        if (!$valid) {
            throw new \InvalidArgumentException('Invalid timestamp period from ' . $from . ' to ' . $to);
        }
        $this->from = $from;
        $this->to = $to;
        $this->logger = $logger;
    }

    public function match(array $messageOverview): bool
    {
        $timestamp = $this->getTimestamp($messageOverview);
        return $timestamp >= $this->from && $timestamp <= $this->to;
    }

    private function validate(int $from, int $to): bool
    {
        return $from < 0 || $to < 0 || $from > $to;
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
