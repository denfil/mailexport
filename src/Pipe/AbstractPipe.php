<?php

declare(strict_types=1);

namespace MailExport\Pipe;

use MailExport\Map;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractPipe implements LoggerAwareInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    abstract public function __invoke(Map $messages): Map;
}
