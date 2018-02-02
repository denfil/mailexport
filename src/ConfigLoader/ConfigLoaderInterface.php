<?php

declare(strict_types=1);

namespace MailExport\ConfigLoader;

interface ConfigLoaderInterface
{
    public function load(): array;
}
