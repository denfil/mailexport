<?php

declare(strict_types=1);

namespace MailExport;

use Aura\Di\Container;
use Aura\Di\ContainerConfig as BaseContainerConfig;
use MailExport\ConfigLoader\Getopt;
use MailExport\ConfigLoader\PhpArray;
use MailExport\Mailbox\Imap;
use MailExport\Mailbox\MailboxInterface;
use MailExport\Pipe\Archive;
use MailExport\Pipe\Connect;
use MailExport\Pipe\Delete;
use MailExport\Pipe\Disconnect;
use MailExport\Pipe\Download;
use MailExport\Pipe\Select;
use MailExport\Storage\Filesystem;
use MailExport\Storage\StorageInterface;
use Psr\Log\LoggerInterface;

class ContainerConfig extends BaseContainerConfig
{
    /**
     * @inheritdoc
     */
    public function define(Container $di)
    {
        $di->set('pipe_connect', $di->lazyNew(Connect::class));
        $di->set('pipe_disconnect', $di->lazyNew(Disconnect::class));
        $di->set('pipe_select', $di->lazyNew(Select::class));
        $di->set('pipe_download', $di->lazyNew(Download::class));
        $di->set('pipe_delete', $di->lazyNew(Delete::class));
        $di->set('pipe_archive', $di->lazyNew(Archive::class));

        $di->set('config', $di->lazy(function () use ($di) {
            return $di->newInstance(
                Config::class,
                [[
                    $di->newInstance(PhpArray::class, [__DIR__ . '/../config.php']),
                    $di->newInstance(Getopt::class)
                ]]
            );
        }));
        $di->set('logger', $di->lazy(function () use ($di) {
            $config = $di->get('config');
            return $di->newInstance(
                Logger::class,
                [
                    $config->logs_directory,
                    (bool)$config->debug
                ]
            );
        }));
        $di->set('storage', $di->lazy(function () use ($di) {
            $config = $di->get('config');
            return $di->newInstance(
                Filesystem::class,
                [$config->export_directory]
            );
        }));
        $di->set('mailbox', $di->lazy(function () use ($di) {
            $config = $di->get('config');
            return $di->newInstance(
                Imap::class,
                [
                    $config->host,
                    $config->username,
                    $config->password,
                    $config->mailbox
                ]
            );
        }));

        $di->params[Select::class]['period'] = $di->lazy(function () use ($di) {
            $config = $di->get('config');
            $from = $config->from ? strtotime($config->from) : null;
            $to = $config->to ? strtotime($config->to) : null;
            return $di->newInstance(Period::class, [$from, $to]);
        });

        $di->types[LoggerInterface::class] = $di->lazyGet('logger');
        $di->types[StorageInterface::class] = $di->lazyGet('storage');
        $di->types[MailboxInterface::class] = $di->lazyGet('mailbox');
    }
}
