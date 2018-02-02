<?php

declare(strict_types=1);

namespace MailExport;

use MailExport\ConfigLoader\ConfigLoaderInterface;

/**
 * @property string $host
 * @property string $username
 * @property string $password
 * @property string $mailbox
 * @property string $export_directory
 * @property string $logs_directory
 * @property int $from
 * @property int $to
 * @property bool $delete
 * @property bool $archive
 * @property bool $debug
 */
class Config
{
    /**
     * @var array
     */
    private $config = [];

    //public function __construct(ConfigLoaderInterface ...$loaders)
    public function __construct(array $loaders)
    {
        if (empty($loaders)) {
            throw new \InvalidArgumentException('Loaders are not specified');
        }
        foreach ($loaders as $loader) {
            $this->config = array_merge($this->config, $loader->load());
        }
        /*
        $from = $this->config['from'] ? strtotime($this->config['from']) : null;
        $to = $this->config['to'] ? strtotime($this->config['to']) : null;
        $this->config['period'] = new Period($from, $to);
        unset($this->config['from'], $this->config['to']);
        */
    }

    public function __get($name)
    {
        return isset($this->config[$name]) ? $this->config[$name] : null;
    }

    public function toArray()
    {
        return $this->config;
    }
}
