<?php

declare(strict_types=1);

namespace MailExport\Matcher;

interface MatcherInterface
{
    public function match(array $messageOverview): bool;
}
