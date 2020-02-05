<?php

declare(strict_types=1);

namespace Shays\Logger;

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
	const LEVELS = [
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
		return [
			self::DEBUG,
			self::INFO,
			self::NOTICE,
			self::WARNING,
			self::ERROR,
			self::CRITICAL,
			self::ALERT,
			self::EMERGENCY,
		];
	}

	/**
	 * Gets a log level name
	 *
	 * @param int $level
	 * @return string
	 */
	public static function getLevelName(int $level): string
	{
		return self::LEVELS[$level];
	}
}