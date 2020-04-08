<?php

namespace Schalkt\Slog\Tests;

use PHPUnit\Framework\TestCase;
use Schalkt\Slog\Log;

final class LogTest extends TestCase
{
		
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
		$this->assertTrue(strpos($log, ' | INFO --- Test info') === 19);
	
	}

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
		$this->assertTrue(strpos($log, ' | ERROR --- Test error') === 19);
	
	}

	public function testCustomWarning()
	{
		
		// delete default log folder
		Log::type()->flush();

		$config = [
			"pattern_file" => "/{YEAR}-{MONTH}/{TYPE}-{YEAR}-{MONTH}-{DAY}-{STATUS}.log",
			"pattern_row" => "{DATE} ### {STATUS} ### {MESSAGE}",
		];

		// default config
		Log::type('payment', $config)->warning('Test warning');

		// concat logfile path
		$logPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR;
		$logFile = $logPath . date('Y') . '-' . date('m') . DIRECTORY_SEPARATOR . 'payment-' . date('Y-m-d') . '-WARNING.log';

		// is logfile exists?
		$this->assertTrue(file_exists($logFile));

		// read log file content
		$log = file_get_contents($logFile);

		// is logfile content correct?
		$this->assertTrue(strpos($log, ' ### WARNING ### Test warning') === 19);
	
	}

	public function testLoadConfigAndCSV()
	{
		
		// load config
		Log::configs( __DIR__ . '/config.php');

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
		$this->assertTrue(strpos($log, '"date";"message";"class";"function"') === 0);
		$this->assertTrue(strpos($log, ';CSV message;') === 58);
			
	}

} 