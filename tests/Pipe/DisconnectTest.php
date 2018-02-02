<?php

declare(strict_types=1);

namespace MailExport\Pipe;

use MailExport\Mailbox\MailboxInterface;
use MailExport\Map;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \MailExport\Pipe\Disconnect
 */
class DisconnectTest extends TestCase
{
    /**
     * @covers ::__invoke
     */
    public function testClosesConnectAndReturnsInputMap()
    {
        $messages = new Map([
            369 => 1514817923,
            4766 => 1517535633
        ]);
        $mailbox = $this->createMock(MailboxInterface::class);
        $mailbox->expects($this->once())
            ->method('close');
        $disconnect = new Disconnect($mailbox, new NullLogger());
        $result = $disconnect($messages);
        $this->assertEquals($messages->toArray(), $result->toArray());
    }
}
