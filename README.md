# Simple and easy configurable PHP log system

I decided to write my own log engine, which already has a basic and automatic rotation. Plus, with a simple pattern change, I can write to CSV. And of course, objects and arrays are automatically converted to prettified JSON. So cool :)

## Install

- `composer require schalkt/log`

## Features

- pattern based logfile path: `{YEAR}/{YEAR}-{MONTH}/{TYPE}-{MONTH}-{DAY}.log`
- pattern based rows: `{DATE} | {STATUS} --- {MESSAGE}`
- CSV logfile path: `{YEAR}-{MONTH}/{DAY}.csv`
- CSV row pattern: `'"{DATE}";{MESSAGE};"{BACKTRACE.CLASS}";"{BACKTRACE.FUNCTION}"'`
- objects and arrays convert to prettified JSON automatically

## Examples

### Change default log folder

```php

    use Schalkt\Slog\Log;

    require_once '/vendor/autoload.php';

    Log::configDefault(["folder" => './logs/default']);
    Log::type()->info('Hello World!');

```

### Load custom configs

```php

    use Schalkt\Slog\Log;

    require_once '/vendor/autoload.php';

    // set config file path
    Log::configs('./config/logs.php');

    // add an error to the default log
    Log::type()->error('Password required');

    // add an input array to the login log with title
    Log::type('login')->notice($input, 'Invalid password');

```

## Configs

### Default config

```php
return [
    'default' => [
        "folder" => './logs/default',
        "folder_chmod" => 0770,
        "pattern_file" => "/{YEAR}-{MONTH}/{TYPE}-{YEAR}-{MONTH}-{DAY}.log",
        "pattern_row" => "{DATE} | {STATUS} --- {MESSAGE}",
        "format_date" => 'Y-m-d H:i:s',
    ]
];
```

### Custom config file

```php
return [
    "csv" => [
        "folder" => './logs',
        "header" => '"date";"message";"class";"function"',
        "pattern_file" => "/{TYPE}/{YEAR}-{MONTH}/{TYPE}-{YEAR}-{MONTH}-{DAY}.csv",
        "pattern_row" => '"{DATE}";{MESSAGE};"{BACKTRACE.CLASS}";"{BACKTRACE.FUNCTION}"',
    ],
    "login" => [
        "folder" => './logs',
        "pattern_file" => "/logins/{TYPE}/{YEAR}-{MONTH}-{DAY}.log",
        "pattern_row" => "{DATE} {TITLE} {MESSAGE}",
    ],
];
```

## Available variables

- {MESSAGE} <- first parameter of function (required, string, array, object, any)
- {TITLE} <- second parameter of function (not required, string or number)
- {TYPE} <- came from log config
- {STATUS} <- info, error, critical, warning, notice, debug, or exception
- {REQUEST} <- dump $_REQUEST
- {RAWBODY} <- file_get_contents('php://input')
- {EOL} <- PHP_EOL
- {DATE} <- date by config "format_date", default "Y-m-d H:i:s"
- {YEAR} <- date('Y')
- {MONTH} <- date('m')
- {DAY} <- date('d')
- {HOUR} <- date('H')
- {MIN} <- date('i')
