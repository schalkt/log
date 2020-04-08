# Simple and highly configurable PHP log system

> The project is under development, but this is a working and stable version.

## Install

- ```composer require schalkt\log```

## Examples

```php

    use Schalkt\Log;

    require_once '/vendor/autoload.php';

    // set config file path
    Log::configs('/config/logs.php');
    ...

    // log an error to the default log
    Log::type()->error('Password required');

    // log a notice to the login log
    Log::type('login')->notice($input, 'Invalid password');

```

## TODO

- tests
