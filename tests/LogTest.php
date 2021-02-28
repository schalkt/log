<?php

namespace Schalkt\Slog\Tests;

use PHPUnit\Framework\TestCase;
use Schalkt\Slog\Log;

final class LogTest extends TestCase
{

	/**
	 * exceptionTest
	 *
	 * @param  mixed $method
	 * @param  mixed $msg
	 * @return void
	 */
	protected function exceptionTest($method, $errorMessage)
	{

		try {
			$method();
		} catch (\Exception $e) {
			$this->assertSame($errorMessage, $e->getMessage());
		}
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
		$logPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR;
		$logFile = $logPath . date('Y') . '-' . date('m') . DIRECTORY_SEPARATOR . 'default-' . date('Y-m-d') . '.log';

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
		$logPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR;
		$logFile = $logPath . date('Y') . '-' . date('m') . DIRECTORY_SEPARATOR . 'default-' . date('Y-m-d') . '.log';

		// is logfile exists?
		$this->assertTrue(file_exists($logFile));

		// read log file content
		$log = file_get_contents($logFile);

		// is logfile content correct?
		$this->assertSame(19, strpos($log, ' | ERROR --- Test error'));
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
			"pattern_file" => "/{YEAR}-{MONTH}/{TYPE}-{YEAR}-{MONTH}-{DAY}-{STATUS}.log",
			"pattern_row" => "{DATE} ### {STATUS} ### {MESSAGE}",
		];

		// default config
		Log::type('default', $config)->warning('Test warning');

		// concat logfile path
		$logPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR;
		$logFile = $logPath . date('Y') . '-' . date('m') . DIRECTORY_SEPARATOR . 'default-' . date('Y-m-d') . '-WARNING.log';

		// is logfile exists?
		$this->assertTrue(file_exists($logFile));

		// read log file content
		$log = file_get_contents($logFile);

		// is logfile content correct?
		$this->assertSame(19, strpos($log, ' ### WARNING ### Test warning'));
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
		$logPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'csv' . DIRECTORY_SEPARATOR;
		$logFile = $logPath . date('Y') . '-' . date('m') . DIRECTORY_SEPARATOR . 'csv-' . date('Y-m-d') . '.csv';

		// is logfile exists?
		$this->assertTrue(file_exists($logFile));

		// read log file content
		$log = file_get_contents($logFile);

		// is logfile content correct?
		$this->assertSame(0, strpos($log, '"date";"message";"class";"function"'));
		$this->assertSame(57, strpos($log, ';CSV message;'));
	}


	/**
	 * testExceptions
	 *
	 * @return void
	 */
	public function testExceptions()
	{

		$this->exceptionTest(function () {
			Log::configs("");
		}, "Invalid path of configs");

		$this->exceptionTest(function () {
			Log::type("");
		}, "Empty config type");

		$this->exceptionTest(function () {
			Log::type("defalt")->debug(['param1' => 1], 'Parameters');
		}, "Invalid config type: defalt");
	}
}
