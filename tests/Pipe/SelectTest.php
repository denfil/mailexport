<?php

declare(strict_types=1);

namespace MailExport\Pipe;

use MailExport\Mailbox\MailboxInterface;
use MailExport\Map;
use MailExport\Period;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \MailExport\Pipe\Select
 */
class SelectTest extends TestCase
{
    /**
     * @var Period
     */
    private $period;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->period = new Period(strtotime('2018-01-01'), strtotime('2018-01-02'));
    }

    /**
     * @covers ::__invoke
     */
    public function testWillReturnsEmptyMapIfMailboxErrorOccurred()
    {
        $error = 'Mailbox error message';
        $mailbox = $this->createMock(MailboxInterface::class);
        $mailbox->method('getMessageCount')->willReturn(false);
        $mailbox->method('getError')->willReturn($error);
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with($this->equalTo($error));
        $select = new Select($mailbox, $this->period, $logger);
        $actual = $select(new Map());
        $this->assertEquals(0, count($actual));
    }

    /**
     * @covers ::__invoke
     */
    public function testWillReturnsEmptyMapIfNoMessagesSelected()
    {
        $mailbox = $this->createMock(MailboxInterface::class);
        $mailbox->method('getMessageCount')->willReturn(0);
        $select = new Select($mailbox, $this->period, new NullLogger());
        $actual = $select(new Map());
        $this->assertEquals(0, count($actual));
    }

    /**
     * @covers ::__invoke
     */
    public function testWillThrowExceptionIfGettingMessageOverviewsFailed()
    {
        $error = 'Mailbox error message';
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage($error);
        $mailbox = $this->createMock(MailboxInterface::class);
        $mailbox->method('getMessageCount')->willReturn(2);
        $mailbox->method('getMessageOverviewsByNumbers')->willReturn(false);
        $mailbox->method('getError')->willReturn($error);
        $select = new Select($mailbox, $this->period, new NullLogger());
        $select(new Map());
    }

    /**
     * @covers ::__invoke
     */
    public function testWillReturnsEmptyMapIfNoMessagesMatched()
    {
        $overviews = [
            [
                'uid' => 173,
                'date' => 'Mon, 14 Jan 2008 19:45:08 +0300 (MSK)',
                'received' => 1200329174
            ],
            [
                'uid' => 221,
                'date' => 'Fri, 17 Jul 2009 11:27:36 +0500',
                'received' => 1245657308
            ]
        ];
        $mailbox = $this->createMock(MailboxInterface::class);
        $mailbox->method('getMessageCount')->willReturn(2);
        $mailbox->method('getMessageOverviewsByNumbers')->willReturn($overviews);
        $select = new Select($mailbox, $this->period, new NullLogger());
        $actual = $select(new Map());
        $this->assertEquals(0, count($actual));
    }

    /**
     * @covers ::__invoke
     */
    public function testWillReturnsNonEmptyMapIfAnyMessagesMatched()
    {
        $overviews = [
            [
                'uid' => 221,
                'date' => 'Fri, 17 Jul 2009 11:27:36 +0500',
                'received' => 1245657308
            ],
            [
                'uid' => 369,
                'date' => 'Mon, 01 Jan 2018 14:45:23 +0000 (UTC)',
                'received' => 1514817926
            ],
            [
                'uid' => 4766,
                'date' => 'Fri, 02 Feb 2018 03:40:33 +0200',
                'received' => 1517604979
            ]
        ];
        $mailbox = $this->createMock(MailboxInterface::class);
        $mailbox->method('getMessageCount')->willReturn(2);
        $mailbox->method('getMessageOverviewsByNumbers')->willReturn($overviews);
        $select = new Select($mailbox, $this->period, new NullLogger());
        $actual = $select(new Map());
        $this->assertEquals(1, count($actual));
        $this->assertEquals($overviews[1]['uid'], key(current($actual)));
    }
}
