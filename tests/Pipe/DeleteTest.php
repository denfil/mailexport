<?php

declare(strict_types=1);

namespace MailExport\Pipe;

use MailExport\Mailbox\MailboxInterface;
use MailExport\Map;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \MailExport\Pipe\Delete
 */
class DeleteTest extends TestCase
{
    /**
     * @covers ::__invoke
     */
    public function testWillReturnsEmptyMapIfInputMapIsEmpty()
    {
        $mailbox = $this->createMock(MailboxInterface::class);
        $delete = new Delete($mailbox, new NullLogger());
        $result = $delete(new Map());
        $this->assertEquals(0, count($result));
    }

    /**
     * @covers ::__invoke
     */
    public function testDeletesMessagesAndReturnsInputMap()
    {
        $messages = new Map([
            369 => 1514817923,
            4766 => 1517535633
        ]);
        $mailbox = $this->createMock(MailboxInterface::class);
        $mailbox->expects($this->exactly(2))
            ->method('deleteMessageByUid')
            ->withConsecutive(
                [369],
                [4766]
            );
        $delete = new Delete($mailbox, new NullLogger());
        $result = $delete($messages);
        $this->assertEquals($messages->toArray(), $result->toArray());
    }
}
