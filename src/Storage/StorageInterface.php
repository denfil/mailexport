<?php

declare(strict_types=1);

namespace MailExport\Storage;

interface StorageInterface
{
    public function save(int $uid, int $timestamp, string $message): bool;
    public function archive(string $name = '');
}
