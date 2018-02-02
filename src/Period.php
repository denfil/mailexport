<?php

declare(strict_types=1);

namespace MailExport;

class Period
{
    /**
     * @var int
     */
    private $from;

    /**
     * @var int
     */
    private $to;

    public function __construct(int $from = null, int $to = null)
    {
        if ($from !== null || $to !== null) {
            $this->from = $from ?? 0;
            $this->to = $to ?? $this->from;
        }
        if ($this->from < 0 || $this->to < 0 || $this->from > $this->to) {
            throw new \InvalidArgumentException('Invalid timestamp period from ' . $this->from . ' to ' . $this->to);
        }
    }

    public function from()
    {
        return $this->from;
    }

    public function to()
    {
        return $this->to;
    }

    public function contains(int $timestamp): bool
    {
        return $timestamp >= $this->from && $timestamp <= $this->to;
    }
}
