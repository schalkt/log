# Simple and easy configurable PHP log system

I decided to write my own log engine, which already has a basic and automatic rotation. Plus, with a simple pattern change, I can write to CSV. And of course, objects and arrays are automatically converted to prettified JSON. So cool :)

[![Latest Stable Version](https://poser.pugx.org/schalkt/log/v)](//packagist.org/packages/schalkt/log) [![Total Downloads](https://poser.pugx.org/schalkt/log/downloads)](//packagist.org/packages/schalkt/log) [![License](https://poser.pugx.org/schalkt/log/license)](//packagist.org/packages/schalkt/log)
[![GitHub issues](https://img.shields.io/github/issues/schalkt/log.svg?style=flat-square)](https://github.com/schalkt/log/issues)
[![Build Status](https://travis-ci.org/schalkt/log.svg?branch=master)](https://travis-ci.org/schalkt/log)


[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=schalkt_log&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=schalkt_log)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=schalkt_log&metric=security_rating)](https://sonarcloud.io/dashboard?id=schalkt_log)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=schalkt_log&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=schalkt_log)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=schalkt_log&metric=bugs)](https://sonarcloud.io/dashboard?id=schalkt_log)

## Install

- `composer require schalkt/log`

## Features

- pattern based logfile path: `/{TYPE}/{YEAR}/{YEAR}-{MONTH}/{TYPE}-{MONTH}-{DAY}`
- pattern based rows: `{DATE} | {STATUS} --- {MESSAGE}`
- CSV row pattern: `'"{DATE}";{MESSAGE};"{BACKTRACE.CLASS}";"{BACKTRACE.FUNCTION}"'`
- objects and arrays convert to prettified JSON automatically

## Examples

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

    Log::default(["folder" => './logs']);
    Log::to()->info('Hello World!');

```

### Load custom configs

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
        "folder" => './logs/default',
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
        "folder" => './logs',
        "header" => '"date";"message";"class";"function"',
        "pattern_file" => "/{TYPE}/{YEAR}-{MONTH}/{TYPE}-{YEAR}-{MONTH}-{DAY}",
        "pattern_row" => '"{DATE}";{MESSAGE};"{BACKTRACE.CLASS}";"{BACKTRACE.FUNCTION}"',
        "extension" => "csv",
    ],
    "login" => [
        "folder" => './logs',
        "pattern_file" => "/logins/{TYPE}/{YEAR}-{MONTH}-{DAY}",
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

## Todo

- Log::to('login')->info(...);
- configurable output (STDOUT, STDERR)
