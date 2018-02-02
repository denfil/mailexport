<?php

declare(strict_types=1);

namespace MailExport\Pipe;

use MailExport\Mailbox\MailboxInterface;
use MailExport\Map;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \MailExport\Pipe\Connect
 */
class ConnectTest extends TestCase
{
    /**
     * @covers ::__invoke
     */
    public function testOpensConnectAndReturnsInputMap()
    {
        $messages = new Map([
            369 => 1514817923,
            4766 => 1517535633
        ]);
        $mailbox = $this->createMock(MailboxInterface::class);
        $mailbox->expects($this->once())
            ->method('open');
        $connect = new Connect($mailbox, new NullLogger());
        $result = $connect($messages);
        $this->assertEquals($messages->toArray(), $result->toArray());
    }
}
