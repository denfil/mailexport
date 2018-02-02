<?php

declare(strict_types=1);

namespace MailExport\Pipe;

use MailExport\Map;
use MailExport\Storage\StorageInterface;
use Psr\Log\LoggerInterface;

class Archive extends AbstractPipe
{
    /**
     * @var StorageInterface
     */
    private $storage;

    public function __construct(StorageInterface $storage, LoggerInterface $logger)
    {
        parent::__construct($logger);
        $this->storage = $storage;
    }

    public function __invoke(Map $messages): Map
    {
        if (count($messages) == 0) {
            return $messages;
        }
        $this->logger->debug('Creating messages archive');
        $this->storage->archive();
        return $messages;
    }
}
