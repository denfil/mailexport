<?php

return [
    'host' => '',
    'username' => '',
    'password' => '',
    'mailbox' => 'INBOX',
    'export_directory' => __DIR__ . DIRECTORY_SEPARATOR . 'export',
    'logs_directory' => __DIR__ . DIRECTORY_SEPARATOR . 'logs',
    'from' => date('Y-01-01 00:00:00', strtotime('-1 year')),
    'to' => date('Y-12-31 23:59:59', strtotime('-1 year')),
    'delete' => false,
    'archive' => false,
    'debug' => false
];
