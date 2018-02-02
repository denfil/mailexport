<?php

declare(strict_types=1);

namespace MailExport\Storage;

class Filesystem implements StorageInterface
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var string
     */
    private $extension;

    public function __construct(string $directory = null, string $extension = null)
    {
        $this->setDirectory($directory ?? __DIR__);
        $this->setExtension($extension ?? 'eml');
    }

    public function save(int $uid, int $timestamp, string $message): bool
    {
        $filename = $this->directory . $timestamp . '_' . $uid . '.eml';
        $success = file_put_contents($filename, $message);
        if (!$success) {
            throw new \RuntimeException('Error: cannot create file ' . $filename);
        }
        return (bool)$success;
    }

    public function archive(string $name = '')
    {
        $name = $name ?: basename($this->directory);
        $filename = $this->directory . $name . '.tar';
        $archive = new \PharData($filename);
        $regex = $this->extension ? '/' . preg_quote($this->extension) . '$/' : null;
        $archive->buildFromDirectory($this->directory, $regex);
        $archive->compress(\Phar::GZ);
        unlink($filename);
    }

    private function setDirectory(string $directory)
    {
        $path = realpath($directory);
        if ($path === false || !is_dir($path) || !is_writable($path)) {
            throw new \InvalidArgumentException('Directory "' . $directory . '" is now writable');
        }
        $this->directory = $path . DIRECTORY_SEPARATOR;
    }

    private function setExtension(string $extension)
    {
        if (!empty($extension) && mb_substr($extension, 0, 1) != '.') {
            $extension = '.' . $extension;
        }
        $this->extension = $extension;
    }
}
