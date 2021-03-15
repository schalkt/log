<?php

namespace Schalkt\Slog;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Simple and easy configurable PHP logfile system
 */
class Log
{

	const INFO = 'INFO';
	const DEBUG = 'DEBUG';
	const ERROR = 'ERROR';
	const NOTICE = 'NOTICE';
	const WARNING = 'WARNING';
	const CRITICAL = 'CRITICAL';
	const EXCEPTION = 'EXCEPTION';
	const DS = \DIRECTORY_SEPARATOR;

	protected static $configs = [];
	protected static $default = [
		'folder' => '.' . self::DS . 'logs',
		'folder_chmod' => 0770,
		'pattern_file' => self::DS . '{TYPE}' . self::DS . '{YEAR}-{MONTH}' . self::DS . '{TYPE}-{YEAR}-{MONTH}-{DAY}',
		'pattern_row' => '{DATE} | {STATUS} --- {MESSAGE}',
		'extension' => 'log',
		'format_date' => 'Y-m-d H:i:s',
	];

	protected $type = 'default';
	protected $filepath;
	protected $config;
	protected $status = self::INFO;

	/**
	 * Set the configs by config file path or configs array
	 *
	 * @param mixed $configs
	 * @return void
	 */
	public static function configs($configs)
	{

		if (empty($configs)) {
			throw new LogException('Invalid config file path or configs array');
		}

		if (is_array($configs)) {
			self::$configs = $configs;
		} else {

			if (!\file_exists($configs)) {
				throw new LogException('Invalid config file path');
			}

			self::$configs = require($configs);

			if (!\is_array(self::$configs)) {
				throw new LogException('Invalid config file path');
			}
		}

		self::initialize();
	}


	/**
	 * initialize
	 *
	 * @return void
	 */
	protected static function initialize()
	{

		// refresh default config
		if (isset(self::$configs['default'])) {
			$default = array_replace_recursive(self::$default, self::$configs['default']);
			self::$configs['default'] = $default;
		} else {
			self::$configs['default'] = self::$default;
		}

		foreach (self::$configs as $name => $configs) {

			if ($name === 'default') {
				continue;
			}

			self::config($name, $configs, self::$configs['default']);
		}
	}


	/**
	 * Set config by nem and options
	 *
	 * @param  string $name
	 * @param  array $options
	 * @param  array $default
	 * @return void
	 */
	public static function config(string $name, array $options, array $default = null)
	{
		$default = $default ? $default : self::$default;
		self::$configs[$name] = array_replace_recursive($default, $options);
	}


	/**
	 * Update default config
	 *
	 * @return array
	 */
	public static function default($options = [])
	{

		self::$default = array_replace_recursive(self::$default, $options);
		self::$configs['default'] = self::$default;

		return self::$default;
	}


	/**
	 * Update default config
	 * 
	 * @deprecated
	 * 
	 * @return array
	 */
	public static function configDefault($config = [])
	{

		return self::default($config);
	}


	/**
	 * Reset configs
	 *
	 * @return void
	 */
	public static function reset()
	{

		self::$configs = [];
	}


	/**
	 * to
	 *
	 * @param  mixed $type
	 * @param  mixed $config
	 * @return Log
	 */
	public static function to($type = 'default', $config = [])
	{
		return self::type($type, $config);
	}


	/**
	 * Set log type and return with new Log class
	 *
	 * @param string $type
	 * @return Log
	 */
	public static function type($type = 'default', $config = [])
	{

		if (empty($type)) {
			$type = 'default';
		}

		if (empty(self::$configs)) {
			self::$configs['default'] = self::$default;
		}

		if (!isset(self::$configs['default'])) {
			self::$configs['default'] = self::$default;
		}

		if (!isset(self::$configs[$type])) {
			self::$configs[$type] = self::$configs['default'];
		}

		self::$configs[$type] = array_replace_recursive(self::$configs['default'], self::$configs[$type], $config);

		return new Log(self::$configs[$type], $type);
	}

	/**
	 * Construct new class and initialize
	 *
	 * @param array $config
	 */
	function __construct($config, $type)
	{

		$this->type = trim((string) $type);
		$this->config = $config;
		$this->setPath();
	}

	/**
	 * Set file path and mkdir if not exists
	 *
	 * @return void
	 */
	protected function setPath()
	{

		$this->filepath = $this->config['folder'] . $this->setVariables($this->config['pattern_file']) . '.' . $this->config['extension'];

		$basefolder = pathinfo($this->filepath, PATHINFO_DIRNAME);

		if (!file_exists($basefolder)) {
			mkdir($basefolder, !empty($this->config['folder_chmod']) ? $this->config['folder_chmod'] : 0770, true);
		}
	}


	/**
	 * Add a new message to the log
	 *
	 * @param string $message
	 * @return void
	 */
	protected function addMessage($message, $status = null, $title = null)
	{

		if (!is_string($message)) {
			$message = $this->json($message);
		}

		$this->status = $status !== null ? $status : self::INFO;
		$this->setPath();

		$row = $this->setVariables($this->config['pattern_row'], $message, $title);

		if (!file_exists($this->filepath) && !empty($this->config['header'])) {
			file_put_contents($this->filepath, $this->config['header'] . PHP_EOL, FILE_APPEND);
		}

		file_put_contents($this->filepath, $row . PHP_EOL, FILE_APPEND);

		return $this;
	}

	/**
	 * Convert array or object to json
	 *
	 * @param [type] $data
	 * @return void
	 */
	protected function json($data)
	{

		return json_encode($data, JSON_PRETTY_PRINT, JSON_UNESCAPED_UNICODE);
	}

	/**
	 * Add INFO message
	 *
	 * @param string $message
	 * @return void
	 */
	public function info($message, $title = null)
	{
		return $this->addMessage($message, self::INFO, $title);
	}

	/**
	 * Add ERROR message
	 *
	 * @param string $message
	 * @return void
	 */
	public function error($message, $title = null)
	{
		return $this->addMessage($message, self::ERROR, $title);
	}

	/**
	 * Add CRITICAL message
	 *
	 * @param string $message
	 * @return void
	 */
	public function critical($message, $title = null)
	{
		return $this->addMessage($message, self::CRITICAL, $title);
	}

	/**
	 * Add WARNING message
	 *
	 * @param string $message
	 * @return void
	 */
	public function warning($message, $title = null)
	{
		return $this->addMessage($message, self::WARNING, $title);
	}

	/**
	 * Add NOTICE message
	 *
	 * @param string $message
	 * @return void
	 */
	public function notice($message, $title = null)
	{
		return $this->addMessage($message, self::NOTICE, $title);
	}

	/**
	 * Add DEBUG message
	 *
	 * @param string $message
	 * @return void
	 */
	public function debug($message, $title = null)
	{
		return $this->addMessage($message, self::DEBUG, $title);
	}

	/**
	 * Add EXCEPTION message
	 *
	 * @param string $message
	 * @return void
	 */
	public function exception(\Exception $ex, $title = null)
	{
		return $this->addMessage([$ex->getMessage(), $ex->getFile(), $ex->getLine()], self::EXCEPTION, $title);
	}

	/**
	 * Set variables in file or message pattern
	 *
	 * @param string $pattern
	 * @param string $message
	 * @return void
	 */
	protected function setVariables($pattern, $message = null, $title = null)
	{

		preg_match_all("/{SERVER\.(.*)}/ismU", $pattern, $matchesSERVER, PREG_SET_ORDER);

		if (!empty($matchesSERVER[0])) {
			foreach ($matchesSERVER as $match) {
				if (isset($_SERVER[$match[1]])) {
					$pattern = str_replace($match[0], $_SERVER[$match[1]], $pattern);
				}
			}
		}

		preg_match_all("/{BACKTRACE\.(.*)}/ismU", $pattern, $matchesBACKTRACE, PREG_SET_ORDER);

		if (!empty($matchesBACKTRACE[0])) {

			$backtrace = debug_backtrace()[3];

			foreach ($matchesBACKTRACE as $match) {
				if (isset($backtrace[strtolower($match[1])])) {
					$pattern = str_replace($match[0], $backtrace[strtolower($match[1])], $pattern);
				}
			}
		}

		return str_replace([
			'{YEAR}',
			'{MONTH}',
			'{DAY}',
			'{HOUR}',
			'{MIN}',
			'{DATE}',
			'{MESSAGE}',
			'{TITLE}',
			'{TYPE}',
			'{STATUS}',
			'{REQUEST}',
			'{RAWBODY}',
			'{EOL}',
		], [
			date('Y'),
			date('m'),
			date('d'),
			date('H'),
			date('i'),
			date($this->config['format_date']),
			trim($message),
			trim($title),
			$this->type,
			$this->status,
			$this->json($_REQUEST),
			file_get_contents('php://input'),
			PHP_EOL
		], $pattern);
	}

	/**
	 * Delete all logfiles under type
	 *
	 * @param string $message
	 * @return void
	 */
	public function flush()
	{

		foreach (self::$configs as $config) {

			if (file_exists($config['folder']) && is_dir($config['folder'])) {

				if (in_array($config['folder'], ['.', './', '.\\', '..'], true)) {
					throw new \Exception('Protected folder cannot remove');
				}

				$this->rrmdir($config['folder']);
			}
		}
	}

	/**
	 * Recursive folder delete
	 *
	 * @param  mixed $dir
	 * @return void
	 */
	protected function rrmdir($dir)
	{

		$iterator = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
		$iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);

		foreach ($iterator as $file) {

			if ($file->isDir()) {
				rmdir($file->getPathname());
			} else {
				unlink($file->getPathname());
			}
		}

		rmdir($dir);
	}
}
