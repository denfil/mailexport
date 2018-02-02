<?php

declare(strict_types=1);

namespace MailExport\Mailbox;

class Imap implements MailboxInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var resource
     */
    private $imap;

    /**
     * @var string
     */
    private $error = '';

    public function __construct(string $host, string $username, string $password, string $mailbox)
    {
        $this->config = [
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'mailbox' => $mailbox
        ];
    }

    public function __destruct()
    {
        $this->close();
    }

    public function open()
    {
        if (is_resource($this->imap)) {
            return;
        }
        $mailbox = mb_convert_encoding($this->config['mailbox'], 'UTF7-IMAP', 'UTF-8');
        $this->imap = imap_open(
            '{' . $this->config['host'] . '}'. $mailbox,
            $this->config['username'],
            $this->config['password']
        );
        if ($this->imap === false) {
            $this->setError('Failed to connect to ' . $this->config['host']);
            throw new \RuntimeException($this->getError());
        }
    }

    public function close()
    {
        if (is_resource($this->imap)) {
            imap_close($this->imap, CL_EXPUNGE);
        }
        $this->imap = null;
        $this->error = '';
    }

    public function getMessageCount()
    {
        $result = imap_num_msg($this->imap);
        if ($result === false) {
            $this->setError('Failed to get message count');
        }
        return $result;
    }

    public function getMessageOverviewsByNumbers(string $sequence)
    {
        $messages = imap_fetch_overview($this->imap, $sequence);
        if (empty($messages)) {
            $this->setError('Failed to get message overviews ' . $sequence);
            return false;
        }
        $result = [];
        foreach ($messages as $message) {
            $result[] = [
                'uid' => $message->uid,
                'date' => $message->date,
                'received' => $message->udate
            ];
        }
        return $result;
    }

    public function getMessageSourceByUid(int $uid)
    {
        $header = imap_fetchheader($this->imap, $uid, FT_UID);
        if ($header === false) {
            $this->setError('Failed to get header of message UID ' . $uid);
            return false;
        }
        $body = imap_body($this->imap, $uid, FT_UID | FT_PEEK);
        if ($body === false) {
            $this->setError('Failed to get body of message UID ' . $uid);
            return false;
        }
        return $header . $body;
    }

    public function deleteMessageByUid(int $uid)
    {
        imap_delete($this->imap, $uid, FT_UID);
    }

    public function getError()
    {
        return $this->error;
    }

    private function setError(string $message)
    {
        $this->error = $message . ': ' . imap_last_error();
    }
}
