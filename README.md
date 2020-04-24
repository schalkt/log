# Simple and easy configurable PHP log system

> The project is under development, but this is a working and stable version.

## Install

- `composer require schalkt\log`

## Features

- pattern based logfile path: `{YEAR}/{TYPE}-{MONTH}-{DAY}.log`
- pattern based rows: `{DATE} | {STATUS} --- {MESSAGE}`
- CSV row pattern: ``'"{DATE}";{MESSAGE};"{BACKTRACE.CLASS}";"{BACKTRACE.FUNCTION}"'``

## Examples

### Default config

```php
[
    "folder" => './logs/default',
    "folder_chmod" => 0770,
    "pattern_file" => "/{YEAR}-{MONTH}/{TYPE}-{YEAR}-{MONTH}-{DAY}.log",
    "pattern_row" => "{DATE} | {STATUS} --- {MESSAGE}",
    "format_date" => 'Y-m-d H:i:s',
];
```

- see example (CSV) config file under the /tests folder

### Basic

```php

    require_once '/vendor/autoload.php';

    \Schalkt\Log::configDefault(["folder" => './logs/default']);
    \Schalkt\Log::type()->info('Hello World!');

```

### Custom config

```php

    use Schalkt\Log;

    require_once '/vendor/autoload.php';

    // set config file path
    Log::configs('./config/logs.php');

    // add an error to the default log
    Log::type()->error('Password required');

    // add a notice to the login log
    Log::type('login')->notice($input, 'Invalid password');

```
