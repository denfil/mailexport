<?php

declare(strict_types=1);

namespace MailExport\ConfigLoader;

class Getopt implements ConfigLoaderInterface
{
    /**
     * @var array
     */
    private $config = [];

    public function __construct()
    {
        $opts = [
            'h:' => 'host:',
            'u:' => 'username:',
            'p:' => 'password:',
            'm:' => 'mailbox:',
            'e:' => 'export-dir:',
            'l:' => 'logs-dir:',
            'f:' => 'from:',
            't:' => 'to:',
            'd' => 'delete',
            'a' => 'archive'
        ];
        $options = getopt(implode('', array_keys($opts)), array_merge($opts, ['debug', 'help']));
        if ($options === false) {
            throw new \RuntimeException('Parsing options error');
        }
        $this->config = $this->parseOptions($options);
        if (isset($options['help'])) {
            $this->showHelp();
            exit;
        }
    }

    public function load(): array
    {
        return $this->config;
    }

    private function parseOptions(array $options): array
    {
        $result = [];
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'h':
                case 'host':
                    $result['host'] = $value;
                    break;
                case 'u':
                case 'username':
                    $result['username'] = $value;
                    break;
                case 'p':
                case 'password':
                    $result['password'] = $value;
                    break;
                case 'm':
                case 'mailbox':
                    $result['mailbox'] = $value;
                    break;
                case 'e':
                case 'export-dir':
                    $result['export_directory'] = $value;
                    break;
                case 'l':
                case 'logs-dir':
                    $result['logs_directory'] = $value;
                    break;
                case 'f':
                case 'from':
                    $result['from'] = $value;
                    break;
                case 't':
                case 'to':
                    $result['to'] = $value;
                    break;
                case 'd':
                case 'delete':
                    $result['delete'] = true;
                    break;
                case 'a':
                case 'archive':
                    $result['archive'] = true;
                    break;
                case 'debug':
                    $result['debug'] = true;
                    break;
            }
        }
        return $result;
    }

    private function showHelp()
    {
        echo "Usage: mailexport [options]

Options:
  -h, --host=NAME          IMAP host, e.g. imap.gmail.com:993/imap/ssl.
  -u, --username=NAME      Username to use when connecting to server..
  -p, --password=NAME      Password to use when connecting to server.
  -m, --mailbox=NAME       Mailbox name.
  -e, --export-dir=PATH    Path to directory with exported messages.
  -l, --logs-dir=PATH      Path to directory with logs.
  -f, --from=DATETIME      Export messages from date.
  -t, --to=DATETIME        Export messages to date.
  -d, --delete             Delete exported messages from mailbox.
  -a, --archive            Archive exported messages.
  --debug                  Logging debug information.
  --help                   Prints this information.
";
    }
}
