<?php

declare(strict_types=1);

namespace MailExport\Pipe;

use MailExport\Map;
use MailExport\Storage\StorageInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \MailExport\Pipe\Archive
 */
class ArchiveTest extends TestCase
{
    /**
     * @covers ::__invoke
     */
    public function testWillReturnsEmptyMapIfInputMapIsEmpty()
    {
        $storage = $this->createMock(StorageInterface::class);
        $archive = new Archive($storage, new NullLogger());
        $result = $archive(new Map());
        $this->assertEquals(0, count($result));
    }

    /**
     * @covers ::__invoke
     */
    public function testArchivesMessagesAndReturnsInputMap()
    {
        $messages = new Map([
            369 => 1514817923,
            4766 => 1517535633
        ]);
        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->once())
            ->method('archive');
        $archive = new Archive($storage, new NullLogger());
        $result = $archive($messages);
        $this->assertEquals($messages->toArray(), $result->toArray());
    }
}
