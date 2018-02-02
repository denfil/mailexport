<?php

declare(strict_types=1);

namespace MailExport;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class Logger extends AbstractLogger
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var bool
     */
    private $debug;

    public function __construct(string $directory, bool $debug = false)
    {
        $path = realpath($directory);
        if ($path === false || !is_dir($path) || !is_writable($path)) {
            throw new \InvalidArgumentException('Directory "' . $directory . '" is not writable');
        }
        $this->directory = $path . DIRECTORY_SEPARATOR;
        $this->debug = (bool)$debug;
    }

    /**
     * @inheritdoc
     */
    public function log($level, $message, array $context = array())
    {
        if ($level === LogLevel::DEBUG && $this->debug === false) {
            return true;
        }
        $log = [
            date('c'),
            gethostname(),
            strtoupper($level),
            $message,
            json_encode($context)
        ];
        $traceable = [
            LogLevel::EMERGENCY => true,
            LogLevel::ALERT => true,
            LogLevel::CRITICAL => true,
            LogLevel::ERROR => true
        ];
        if (isset($traceable[$level])) {
            $log[] = json_encode(debug_backtrace());
        }
        $message = implode(' | ', $log) . PHP_EOL;
        $debug = [
            LogLevel::INFO => true,
            LogLevel::DEBUG => true
        ];
        $filename = isset($debug[$level]) ? 'debug.log' : 'error.log';
        $result = file_put_contents($this->directory . $filename, $message, FILE_APPEND | LOCK_EX);
        return (bool)$result;
    }
}
