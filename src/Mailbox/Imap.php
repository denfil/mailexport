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
            //OP_READONLY
        );
        if ($this->imap === false) {
            $this->setError('Failed to connect to ' . $this->config['host']);
            throw new \RuntimeException($this->getError());
        }
    }

    public function reopen(string $mailbox)
    {
        $mailbox = mb_convert_encoding($mailbox, 'UTF7-IMAP', 'UTF-8');
        $success = imap_reopen(
            $this->imap,
            '{' . $this->config['host'] . '}' . $mailbox
            //OP_READONLY
        );
        if ($success === false) {
            $this->setError('Failed to reopen ' . $this->config['host']);
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

    public function getMailboxes()
    {
        //$result = imap_getmailboxes($this->imap, '{' . $this->config['host'] . '}', '*');
        $result = imap_list($this->imap, '{' . $this->config['host'] . '}', '*');
        if ($result === false) {
            $this->setError('Failed to get mailboxes');
        }
        $result = array_map(function (string $mailbox) {
            return str_replace(
                '{' . $this->config['host'] . '}',
                '',
                mb_convert_encoding($mailbox, 'UTF-8', 'UTF7-IMAP')
            );
        }, $result);
        return $result;
    }

    public function getMailboxStatus(string $mailbox)
    {
        $this->reopen($mailbox);
        $info = imap_mailboxmsginfo($this->imap);
        if ($info === false) {
            $this->setError('Failed to get info of mailbox ' . $mailbox);
        }
        $result = [
            'mailbox' => $info ? mb_convert_encoding($info->Mailbox, 'UTF-8', 'UTF7-IMAP') : null,
            'driver' => $info ? $info->Driver : null,
            'flags' => null,
            'size' => $info ? $info->Size : null,
            'date' => $info ? $info->Date : null,
            'next_uid' => null,
            'uid_validity' => null,
            'messages' => $info ? $info->Nmsgs : null,
            'recent' => $info ? $info->Recent : null,
            'unread' => $info ? $info->Recent : null,
            'deleted' => $info ? $info->Deleted : null
        ];
        $mailbox = mb_convert_encoding($mailbox, 'UTF7-IMAP', 'UTF-8');
        $info = imap_status($this->imap, '{' . $this->config['host'] . '}' . $mailbox, SA_ALL);
        if ($info === false) {
            $this->setError('Failed to get status of mailbox ' . $mailbox);
        }
        $result['flags'] = $info ? $info->flags : null;
        $result['next_uid'] = $info ? $info->uidnext : null;
        $result['uid_validity'] = $info ? $info->uidvalidity : null;
        if ($result['messages'] === null && $info) {
            $result['messages'] = $info->messages;
        }
        if ($result['recent'] === null && $info) {
            $result['recent'] = $info->recent;
        }
        if ($result['unread'] === null && $info) {
            $result['unread'] = $info->unseen;
        }
        return $result;
    }

    /*
    public function getQuota(string $mailbox)
    {
        $mailbox = mb_convert_encoding($mailbox, 'UTF7-IMAP', 'UTF-8');
        $quota = imap_get_quotaroot($this->imap, $mailbox);
        //$quota = imap_get_quota($this->imap, $this->config['username'] . '.' . $mailbox);
        if ($quota === false) {
            $this->setError('Failed to get quota of mailbox ' . $mailbox);
        }
        return $quota;
    }
    */

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
