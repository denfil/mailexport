# mailexport

This is a console script to download and archive e-mail messages from mail servers.
It could be usfull to exporting or creating backups of e-mail messages stored in mail services that don't provide export feature.
Script uses IMAP protocol to communicates with mail server.
Messages store in separated text files in their original Internet Message (RFC 2822) format and could be packed in tar.gz archive.
After saving it's possible to delete original messages to increase free space in  mailbox.

## Install

``` bash
$ git clone https://github.com/denfil/mailexport.git
$ cd ./mailexport
$ composer install
```

## Configuration

To configure edit `config.php` file or use command line options.
You could use both of `config.php` and command line options.
In this case command line options will rewrite corresponding values in `config.php`.

### config.php

| Option | Description |
| --- | --- |
| `host` | Server connection string in format of [imap_open](http://php.net/manual/en/function.imap-open.php) server path, e.g. `imap.yandex.ru:143` or `imap.gmail.com:993/imap/ssl`. |
| `username` | Username to use when connecting to server. |
| `password` | Password to use when connecting to server. |
| `mailbox` | Mailbox name, e.g. `INBOX` |
| `from` | Export messages from date. |
| `to` | Export messages to date. |
| `export_directory` | Path to directory with exported messages. |
| `logs_directory` | Path to directory with logs. |
| `delete` | Whether delete exported messages from mailbox. |
| `archive` | Whether archive exported messages. |
| `debug` | Whether logging debug information. |

### Command line options

To view list of command line options use

``` bash
$ ./bin/mailexport --help
```

## Usage

``` bash
$ ./bin/mailexport
```

## Testing

``` bash
$ composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

