<?php

declare(strict_types=1);

namespace Shays\Logger;

use DateTimeImmutable;

class Log implements LogInterface
{
	/** @var string Log channel */
	protected $channel;

	/** @var int Log level */
	protected $level;

	/** @var string Log level name */
	protected $levelName;

	/** @var string Log message */
	protected $message;

	/** @var mixed[] Additional related information */
	protected $context;

	/** @var string Timestamp when the log was created */
	protected $timestamp;

	/**
	 * Log constructor.
	 *
	 * @param string $channel Log channel
	 * @param int $level Log level
	 * @param string $message Log message
	 * @param mixed[] $context Log context
	 * @param DateTimeImmutable $time DateTime
	 */
	final public function __construct(
		string $channel,
		int $level,
		string $message,
		array $context,
		DateTimeImmutable $time
	) {
		$this->channel = $channel;
		$this->level = $level;
		$this->levelName = LogLevel::getLevelName($level);
		$this->message = $message;
		$this->context = $context ?? [];

		// TODO: Move this into a separate class
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
	 * Gets the log channel
	 *
	 * @return string
	 */
	public function getChannel(): string
	{
		return $this->channel;
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
	 * @param string $field
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
	public function toArray(): array
	{
		return [
			'channel' => $this->getChannel(),
			'message' => $this->getMessage(),
			'level' => $this->getLevel(),
			'levelName' => $this->getLevelName(),
			'context' => $this->getAllContext(),
			'timestamp' => $this->getTimestamp(),
		];
	}
}
