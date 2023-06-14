# Simple and easy configurable PHP log system

A simple log system with pattern based path and messages. Objects and arrays are automatically converted to prettified JSON. You can also create CVS files. No need log rotation, just delete the older log folders if necessary.

[![Latest Stable Version](https://poser.pugx.org/schalkt/log/v)](//packagist.org/packages/schalkt/log) [![Total Downloads](https://poser.pugx.org/schalkt/log/downloads)](//packagist.org/packages/schalkt/log) [![License](https://poser.pugx.org/schalkt/log/license)](//packagist.org/packages/schalkt/log)
[![GitHub issues](https://img.shields.io/github/issues/schalkt/log.svg?style=flat-square)](https://github.com/schalkt/log/issues)
[![Test](https://github.com/schalkt/log/actions/workflows/ci.yml/badge.svg)](https://github.com/schalkt/log/actions/workflows/ci.yml)

[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=schalkt_log&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=schalkt_log)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=schalkt_log&metric=security_rating)](https://sonarcloud.io/dashboard?id=schalkt_log)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=schalkt_log&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=schalkt_log)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=schalkt_log&metric=bugs)](https://sonarcloud.io/dashboard?id=schalkt_log)

## Install

- `composer require schalkt/log`

## Features

- pattern based logfile path: `/{TYPE}/{YEAR}/{YEAR}-{MONTH}/{TYPE}-{MONTH}-{DAY}`
- pattern based rows: `{DATE} | {STATUS} --- {MESSAGE}`
- objects and arrays converted to prettified JSON automatically
- customizable CSV row pattern: `'"{DATE}";{MESSAGE};"{BACKTRACE.CLASS}";"{BACKTRACE.FUNCTION}"'`
- multiple log types in config

## Available log levels

- `Log::to()->info($message, $title = null);`
- `Log::to()->error($message, $title = null);`
- `Log::to()->critical($message, $title = null);`
- `Log::to()->warning($message, $title = null);`
- `Log::to()->notice($message, $title = null);`
- `Log::to()->debug($message, $title = null);`
- `Log::to()->exception(\Exception $ex, $title = null);`

## Examples

Example folder structure with {TYPE}, {YEAR}, {MONTH} and {DATE} patterns

```bash
/storage/logs
    - /default
        - 2021
        - 2022
    - /logins
        - /2021
        - /2022
            - /2022-09
            - /2022-10
                - /INFO-2022-10-20.log
                - /INFO-2022-10-21.log
                - /INFO-2022-10-22.log
                - /ERROR-2022-10-22.log
```

### Use default config

```php

    use Schalkt\Slog\Log;

    require_once '/vendor/autoload.php';

    Log::to()->info('Hello World!');

```

### Change default log folder

```php

    use Schalkt\Slog\Log;

    require_once '/vendor/autoload.php';

    Log::default(["folder" => APP_PATH . '/storage/logs/default']);
    Log::to()->info('Hello World!');

```

### Add new config

```php

    use Schalkt\Slog\Log;

    require_once '/vendor/autoload.php';

    Log::config('import', [
        'folder' => APP_PATH . '/storage/logs/import',
        'folder_chmod' => 0700,
        'pattern_row' => '{DATE} {EOL} {STATUS} {EOL} {MESSAGE} {EOL} {REQUEST}',
    ]);

    // add an error to the import log
    Log::to('import')->error('Unique id required');

```

### Load custom configs from file

```php

    use Schalkt\Slog\Log;

    require_once '/vendor/autoload.php';

    // set config file path
    Log::configs('./config/logs.php');

    // add an error to the default log
    Log::to()->error('Password required');

    // add an input array to the login log with title
    Log::to('login')->notice($input, 'Invalid password');

```

## Configs

### Default config

```php
return [
    'default' => [
        "folder" => APP_PATH . '/storage/logs/default',
        "folder_chmod" => 0770,
        "pattern_file" => "/{YEAR}-{MONTH}/{TYPE}-{YEAR}-{MONTH}-{DAY}",
        "pattern_row" => "{DATE} | {STATUS} --- {MESSAGE}",
        "extension" => "log",
        "format_date" => 'Y-m-d H:i:s',
    ]
];
```

### Custom config file

```php
return [
    "csv" => [
        "folder" =>  APP_PATH . '/storage/logs/csv',
        "header" => '"date";"message";"class";"function"',
        "pattern_file" => "/{TYPE}/{YEAR}-{MONTH}/{TYPE}-{YEAR}-{MONTH}-{DAY}",
        "pattern_row" => '"{DATE}";{MESSAGE};"{BACKTRACE.CLASS}";"{BACKTRACE.FUNCTION}"',
        "extension" => "csv",
    ],
    "login" => [
        "folder" => APP_PATH . '/storage/logs/login',
        "pattern_file" => "/logins/{TYPE}/{YEAR}-{MONTH}-{DAY}",
        "pattern_row" => "{DATE} {TITLE} {MESSAGE}",
    ],
];
```

## Available variables in patterns

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

## Todo

- configurable output (STDOUT, STDERR)
