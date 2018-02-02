<?php

declare(strict_types=1);

namespace MailExport\ConfigLoader;

class PhpArray implements ConfigLoaderInterface
{
    /**
     * @var array
     */
    private $config = [];

    public function __construct(string $filename)
    {
        $path = realpath($filename);
        if ($path === false || !is_file($path) || !is_readable($path)) {
            throw new \InvalidArgumentException('File "' . $filename . '" is not readable');
        }
        $this->config = include $path;
    }

    public function load(): array
    {
        return $this->config;
    }
}
