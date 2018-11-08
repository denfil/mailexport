<?php

declare(strict_types=1);

namespace MailExport\ConfigLoader;

class Dotenv implements ConfigLoaderInterface
{
    /**
     * @var array
     */
    private $config = [];

    public function __construct(string $path)
    {
        $path = realpath($path);
        if ($path === false || !is_dir($path)) {
            throw new \InvalidArgumentException('Directory "' . $path . '" is not available');
        }
        if (is_file($path . DIRECTORY_SEPARATOR . '.env')) {
            $dotenv = new \Dotenv\Dotenv($path, '.env');
            $dotenv->load();
            if (isset($_ENV['MBOX_HOST'])) {
                $this->config['host'] = $_ENV['MBOX_HOST'];
            }
            if (isset($_ENV['MBOX_USERNAME'])) {
                $this->config['username'] = $_ENV['MBOX_USERNAME'];
            }
            if (isset($_ENV['MBOX_PASSWORD'])) {
                $this->config['password'] = $_ENV['MBOX_PASSWORD'];
            }
            if (isset($_ENV['MBOX_NAME'])) {
                $this->config['mailbox'] = $_ENV['MBOX_NAME'];
            }
            if (isset($_ENV['EXPORT_DIRECTORY'])) {
                $this->config['export_directory'] = $_ENV['EXPORT_DIRECTORY'];
            }
            if (isset($_ENV['LOGS_DIRECTORY'])) {
                $this->config['logs_directory'] = $_ENV['LOGS_DIRECTORY'];
            }
            if (isset($_ENV['DATE_FROM'])) {
                $this->config['from'] = $_ENV['DATE_FROM'];
            }
            if (isset($_ENV['DATE_TO'])) {
                $this->config['to'] = $_ENV['DATE_TO'];
            }
            if (isset($_ENV['DELETE'])) {
                $this->config['delete'] = filter_var($_ENV['DELETE'], FILTER_VALIDATE_BOOLEAN);
            }
            if (isset($_ENV['ARCHIVE'])) {
                $this->config['archive'] = filter_var($_ENV['ARCHIVE'], FILTER_VALIDATE_BOOLEAN);
            }
            if (isset($_ENV['DEBUG'])) {
                $this->config['debug'] = filter_var($_ENV['DEBUG'], FILTER_VALIDATE_BOOLEAN);
            }
        }
    }

    public function load(): array
    {
        return $this->config;
    }
}
