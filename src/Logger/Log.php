<?php

declare(strict_types=1);

namespace Shays\Logger;

use DateTimeImmutable;

class Log implements LogInterface
{
	/** @var int Log level */
	private $level;

	/** @var string Log level name */
	private $levelName;

	/** @var int Log message */
	private $message;

	/** @var int Additional related information */
	private $context = [];

	/** @var string Timestamp when the log was created */
	private $timestamp;

	/**
	 * Log constructor.
	 *
	 * @param int $level Log level
	 * @param string $message Log message
	 * @param mixed[] $context Log context
	 * @param DateTimeImmutable $time DateTime
	 */
	public function __construct(int $level, string $message, array $context, DateTimeImmutable $time)
	{
		$this->level = $level;
		$this->levelName = LogLevel::getLevelName($level);
		$this->message = $message;
		$this->context = $context;

		// TODO: Move this into a separate class
		$time->setTimestamp(time());
		$this->timestamp = $time->format('Y-m-d H:i:s');
	}

	/**
	 * Gets the log message
	 *
	 * @return string
	 */
	public function getMessage(): string
	{
		return $this->message;
	}

	/**
	 * Gets the log level
	 *
	 * @return int
	 */
	public function getLevel(): int
	{
		return $this->level;
	}

	/**
	 * Gets the log level name
	 *
	 * @return string
	 */
	public function getLevelName(): string
	{
		return $this->levelName;
	}

	/**
	 * Gets all log context (global and log specific)
	 *
	 * @return mixed[]
	 */
	public function getAllContext(): array
	{
		return $this->context;
	}

	/**
	 * Whether the log was added with a specific context key
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasContext(string $name): bool
	{
		return array_key_exists($name, $this->context);
	}

	/**
	 * Gets a log context (if exists)
	 *
	 * @param string $name
	 * @return mixed|null
	 */
	public function getContext(string $name)
	{
		if ($this->hasContext($name)) {
			return $this->context[$name];
		}

		return null;
	}

	/**
	 * Gets the current log timestamp
	 *
	 * @return string
	 */
	public function getTimestamp(): string
	{
		return $this->timestamp;
	}

	/**
	 * Gets a dynamic field from the log (looks at the context by default)
	 *
	 * @param $field
	 * @return mixed
	 */
	public function get(string $field)
	{
		switch ($field) {
			case 'level':
				return $this->getLevel();
			case 'message':
				return $this->getMessage();
			default:
				return $this->getContext($field);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function serialize(): array
	{
		return [
			'message' => $this->getMessage(),
			'level' => $this->getLevel(),
			'levelName' => $this->getLevelName(),
			'context' => $this->getAllContext(),
			'timestamp' => $this->getTimestamp(),
		];
	}
}
