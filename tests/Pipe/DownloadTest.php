<?php

declare(strict_types=1);

namespace MailExport\Pipe;

use MailExport\Mailbox\MailboxInterface;
use MailExport\Map;
use MailExport\Storage\StorageInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \MailExport\Pipe\Download
 */
class DownloadTest extends TestCase
{
    /**
     * @var array
     */
    private $fixtures;

    /**
     * @var Map
     */
    private $messages;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->fixtures = [
            [
                'uid' => 369,
                'ts' => 1514817923,
                'txt' => 'Message with UID 369'
            ],
            [
                'uid' => 4766,
                'ts' => 1517535633,
                'txt' => 'Message with UID 4766'
            ]
        ];

        $this->messages = new Map([
            $this->fixtures[0]['uid'] => $this->fixtures[0]['ts'],
            $this->fixtures[1]['uid'] => $this->fixtures[1]['ts']
        ]);
    }

    /**
     * @covers ::__invoke
     */
    public function testWillReturnsEmptyMapIfInputMapIsEmpty()
    {
        $mailbox = $this->createMock(MailboxInterface::class);
        $storage = $this->createMock(StorageInterface::class);
        $download = new Download($mailbox, $storage, new NullLogger());
        $this->messages->clear();
        $result = $download($this->messages);
        $this->assertEquals(0, count($result));
    }

    /**
     * @covers ::__invoke
     */
    public function testReturnsMapWithoutUndownloadedMessages()
    {
        $mailbox = $this->createMock(MailboxInterface::class);
        $mailbox->method('getMessageSourceByUid')
            ->will($this->returnValueMap([
                [$this->fixtures[0]['uid'], false],
                [$this->fixtures[1]['uid'], $this->fixtures[1]['txt']]
            ]));

        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->once())
            ->method('save')
            ->with(
                $this->equalTo($this->fixtures[1]['uid']),
                $this->equalTo($this->fixtures[1]['ts']),
                $this->equalTo($this->fixtures[1]['txt'])
            )
            ->willReturn(true);

        $download = new Download($mailbox, $storage, new NullLogger());
        $result = $download($this->messages);
        unset($this->messages[$this->fixtures[0]['uid']]);
        $this->assertEquals($this->messages->toArray(), $result->toArray());
    }

    /**
     * @covers ::__invoke
     */
    public function testReturnsMapWithoutUnsavedMessages()
    {
        $mailbox = $this->createMock(MailboxInterface::class);
        $mailbox->method('getMessageSourceByUid')
            ->will($this->returnValueMap([
                [$this->fixtures[0]['uid'], $this->fixtures[0]['txt']],
                [$this->fixtures[1]['uid'], $this->fixtures[1]['txt']]
            ]));

        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->exactly(2))
            ->method('save')
            ->withConsecutive(
                [
                    $this->equalTo($this->fixtures[0]['uid']),
                    $this->equalTo($this->fixtures[0]['ts']),
                    $this->equalTo($this->fixtures[0]['txt'])
                ],
                [
                    $this->equalTo($this->fixtures[1]['uid']),
                    $this->equalTo($this->fixtures[1]['ts']),
                    $this->equalTo($this->fixtures[1]['txt'])
                ]
            )
            ->will($this->returnValueMap([
                [$this->fixtures[0]['uid'], $this->fixtures[0]['ts'], $this->fixtures[0]['txt'], true],
                [$this->fixtures[1]['uid'], $this->fixtures[1]['ts'], $this->fixtures[1]['txt'], false]
            ]));

        $download = new Download($mailbox, $storage, new NullLogger());
        $result = $download($this->messages);
        unset($this->messages[$this->fixtures[1]['uid']]);
        $this->assertEquals($this->messages->toArray(), $result->toArray());
    }

    /**
     * @covers ::__invoke
     */
    public function testReturnsAndSavesDownloadedMessages()
    {
        $mailbox = $this->createMock(MailboxInterface::class);
        $mailbox->method('getMessageSourceByUid')
            ->will($this->returnValueMap([
                [$this->fixtures[0]['uid'], $this->fixtures[0]['txt']],
                [$this->fixtures[1]['uid'], $this->fixtures[1]['txt']]
            ]));

        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->exactly(2))
            ->method('save')
            ->withConsecutive(
                [
                    $this->equalTo($this->fixtures[0]['uid']),
                    $this->equalTo($this->fixtures[0]['ts']),
                    $this->equalTo($this->fixtures[0]['txt'])
                ],
                [
                    $this->equalTo($this->fixtures[1]['uid']),
                    $this->equalTo($this->fixtures[1]['ts']),
                    $this->equalTo($this->fixtures[1]['txt'])
                ]
            )
        ->willReturn(true);

        $download = new Download($mailbox, $storage, new NullLogger());
        $result = $download($this->messages);
        $this->assertEquals($this->messages->toArray(), $result->toArray());
    }
}
