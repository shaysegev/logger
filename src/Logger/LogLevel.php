<?php

declare(strict_types=1);

namespace Shays\Logger;

use Shays\Logger\Exceptions\LogLevelException;

class LogLevel
{
	/** @var int Debug log level */
	const DEBUG = 100;

	/** @var int Info log level */
	const INFO = 200;

	/** @var int Notice log level */
	const NOTICE = 300;

	/** @var int Warning log level */
	const WARNING = 400;

	/** @var int Error log level */
	const ERROR = 500;

	/** @var int Critical log level */
	const CRITICAL = 600;

	/** @var int Alert log level */
	const ALERT = 700;

	/** @var int Emergency log level */
	const EMERGENCY = 800;

	/** @var mixed[] Mapped log levels */
	public static $levels = [
		self::DEBUG => 'DEBUG',
		self::INFO => 'INFO',
		self::NOTICE => 'NOTICE',
		self::WARNING => 'WARNING',
		self::ERROR => 'ERROR',
		self::CRITICAL => 'CRITICAL',
		self::ALERT => 'ALERT',
		self::EMERGENCY => 'EMERGENCY',
	];

	/**
	 * Gets all the mapped log levels
	 *
	 * @return string[]
	 */
	public static function getLogLevels(): array
	{
		return self::$levels;
	}

	/**
	 * Gets a log level name
	 *
	 * @param int $level
	 * @return string
	 * @throws LogLevelException
	 */
	public static function getLevelName(int $level): string
	{
		if (!isset(self::$levels[$level])) {
			throw new LogLevelException("Log level with level $level was not found");
		}

		return self::$levels[$level];
	}

	/**
	 * Get a log level by its log level name
	 *
	 * @param string $levelName
	 * @return int
	 * @throws LogLevelException
	 */
	public static function getLevelByName(string $levelName): int
	{
		$logLevels = array_flip(self::getLogLevels());
		$logName = $logLevels[strtoupper($levelName)];

		if (!$logName) {
			throw new LogLevelException("Log level with name $levelName was not found");
		}

		return $logName;
	}

	/**
	 * Checks whether a log level already exists
	 *
	 * @param string $level
	 * @return bool
	 */
	public static function hasLevelName(string $level): bool
	{
		return in_array(strtoupper($level), array_values(self::getLogLevels()), true);
	}

	/**
	 * Add an additional log level if needed, which can be logged
	 * and handled by custom handlers depending on requirements
	 *
	 * @param int $level Log level
	 * @param string $name Level name
	 * @throws LogLevelException
	 */
	public static function addLevel(int $level, string $name): void
	{
		if (isset(self::$levels[$level])) {
			throw new LogLevelException("Log level $level already exists. Level: " . self::$levels[$level]);
		}

		self::$levels[$level] = strtoupper($name);

		// Sort the logs by level
		ksort(self::$levels);
	}
}
