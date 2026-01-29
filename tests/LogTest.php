<?php

namespace Schalkt\Slog\Tests;

use PHPUnit\Framework\TestCase;
use Schalkt\Slog\Log;

final class LogTest extends TestCase
{

    const DS = DIRECTORY_SEPARATOR;


    /**
     * exceptionTest
     *
     * @param  mixed $method
     * @param  mixed $msg
     * @return void
     */
    protected function exceptionTest($method, $errorMessage)
    {

        $message = '';

        try {
            $method();
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        $this->assertSame($errorMessage, $message);
    }


    /**
     * testDefaultInfo
     *
     * @return void
     */
    public function testDefaultInfo()
    {

        // delete default log folder
        Log::type()->flush();

        // default config
        Log::type()->info('Test info');

        // concat logfile path
        $logPath = dirname(__DIR__) . self::DS . 'logs' . self::DS . 'default' . self::DS;
        $logFile = $logPath . date('Y') . '-' . date('m') . self::DS . 'default-' . date('Y-m-d') . '.log';

        // is logfile exists?
        $this->assertTrue(file_exists($logFile));

        // read log file content
        $log = file_get_contents($logFile);

        // is logfile content correct?
        $this->assertSame(19, strpos($log, ' | INFO --- Test info'));
    }


    /**
     * testDefaultError
     *
     * @return void
     */
    public function testDefaultError()
    {

        // delete default log folder
        Log::type()->flush();

        // default config
        Log::type()->error('Test error');

        // concat logfile path
        $logPath = dirname(__DIR__) . self::DS . 'logs' . self::DS . 'default' . self::DS;
        $logFile = $logPath . date('Y') . '-' . date('m') . self::DS . 'default-' . date('Y-m-d') . '.log';

        // is logfile exists?
        $this->assertTrue(file_exists($logFile));

        // read log file content
        $log = file_get_contents($logFile);

        // is logfile content correct?
        $this->assertSame(19, strpos($log, ' | ERROR --- Test error'));

        Log::type()->flush();
    }


    /**
     * testCustomWarning
     *
     * @return void
     */
    public function testCustomWarning()
    {

        // delete default log folder
        Log::type()->flush();

        $config = [
            'pattern_file' => '/{TYPE}/{YEAR}-{MONTH}/{TYPE}-{YEAR}-{MONTH}-{DAY}-{STATUS}',
            'pattern_row' => '{DATE} ### {STATUS} ### {MESSAGE}',
        ];

        // default config
        Log::type('default', $config)->warning('Test warning');

        // concat logfile path
        $logPath = dirname(__DIR__) . self::DS . 'logs' . self::DS . 'default' . self::DS;
        $logFile = $logPath . date('Y') . '-' . date('m') . self::DS . 'default-' . date('Y-m-d') . '-WARNING.log';

        // is logfile exists?
        $this->assertTrue(file_exists($logFile));

        // read log file content
        $log = file_get_contents($logFile);

        // is logfile content correct?
        $this->assertSame(19, strpos($log, ' ### WARNING ### Test warning'));

        Log::type()->flush();
    }


    /**
     * testLoadConfigAndCSV
     *
     * @return void
     */
    public function testLoadConfigAndCSV()
    {

        // load config
        Log::configs(__DIR__ . '/logs-config.php');

        // delete csv log folder
        Log::type('csv')->flush();

        // csv config
        Log::type('csv')->info('CSV message');

        // concat logfile path
        $logPath = dirname(__DIR__) . self::DS . 'logs' . self::DS . 'csv' . self::DS;
        $logFile = $logPath . date('Y') . '-' . date('m') . self::DS . 'csv-' . date('Y-m-d') . '.csv';

        // is logfile exists?
        $this->assertTrue(file_exists($logFile));

        // read log file content
        $log = file_get_contents($logFile);

        // is logfile content correct?
        $this->assertSame(0, strpos($log, '"date";"message";"class";"function"'));
        $this->assertSame(57, strpos($log, ';CSV message;'));

        Log::type()->flush();
    }


    /**
     * testExceptions
     *
     * @return void
     */
    public function testExceptions()
    {

        $this->exceptionTest(function () {
            Log::configs('');
        }, 'Invalid config file path or configs array');

        Log::type()->flush();
    }

    /**
     * testTo
     *
     * @return void
     */
    public function testTo()
    {

        // log to undefined config type
        Log::to('something', [
            'pattern_file' => '/{TYPE}/{TYPE}-{YEAR}-{MONTH}-{STATUS}',
        ])->info('Test something');

        $logPath = dirname(__DIR__) . self::DS . 'logs' . self::DS . 'something';
        $logFile = $logPath . self::DS . 'something-' . date('Y-m') . '-INFO.log';

        // is logfile exists?
        $this->assertTrue(file_exists($logFile));

        Log::type()->flush();
    }

    /**
     * testDefaultConfig
     *
     * @return void
     */
    public function testDefaultConfig()
    {

        Log::default([
            'folder' => '.',
            'pattern_file' => '/{TYPE}',
        ]);

        Log::to('world')->info('Hello World!');

        $logFile = dirname(__DIR__) . self::DS . 'world.log';

        // is logfile exists?
        $this->assertTrue(file_exists($logFile));

        // clean up
        unlink($logFile);

    }


    public function testErrorTitle()
    {

        Log::default([
            'pattern_file' => '/{TYPE}',
        ]);


        Log::to('errors')->info('Invalid params');

        $logFile = dirname(__DIR__) . self::DS . 'logs' . self::DS . 'errors.log';

        // is logfile exists?
        $this->assertTrue(file_exists($logFile));

        // read log file content
        $log = file_get_contents($logFile);

        // is logfile content correct?
        $this->assertSame(31, strpos($log, 'Invalid params'));
    }

    public function testNewVariables()
    {
        // Set up a custom configuration
        $config = [
            'folder' => './logs',
            'pattern_file' => '/{YEAR}-{MONTH}-{TYPE}-{STATUS}',
            'pattern_row' => '{IP} {USER_AGENT} {REQUEST_METHOD} {REQUEST_URI} {SESSION_ID} {HOSTNAME} {EXECUTION_TIME} {MEMORY_USAGE} {MEMORY_PEAK_USAGE}',
        ];

        // Apply the configuration
        Log::type('custom', $config)->info('Testing new variables');

        // Define expected log file path
        $logPath = dirname(__DIR__) . self::DS . 'logs';
        $logFile = $logPath . self::DS . date('Y') . '-' . date('m') . '-custom-INFO.log';

        // Check if the log file exists
        $this->assertTrue(file_exists($logFile));

        // Read the log file content
        $logContent = file_get_contents($logFile);

        // Check if the log contains the expected variables
        $this->assertStringContainsString($_SERVER['REMOTE_ADDR'] ?? 'unknown', $logContent);
        $this->assertStringContainsString($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', $logContent);
        $this->assertStringContainsString($_SERVER['REQUEST_METHOD'] ?? 'unknown', $logContent);
        $this->assertStringContainsString($_SERVER['REQUEST_URI'] ?? 'unknown', $logContent);
        $this->assertStringContainsString(session_id() ?? 'no-session', $logContent);
        $this->assertStringContainsString(gethostname(), $logContent);

        // Clean up
        Log::type('custom')->flush();
    }

    protected function setUp(): void
    {
        // Set default folder for tests
        Log::default([
            'folder' => './logs',
        ]);
    }
}
