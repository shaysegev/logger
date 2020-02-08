<?php

declare(strict_types=1);

namespace Shays;

use DateTimeImmutable;
use DateTimeZone;
use Shays\Logger\Contracts\LoggerContract;
use Shays\Logger\Exceptions\CustomLogException;
use Shays\Logger\Exceptions\HandlerException;
use Shays\Logger\Exceptions\InvalidDynamicInvocationException;
use Shays\Logger\Exceptions\LogException;
use Shays\Logger\Exceptions\LogLevelException;
use Shays\Logger\Exceptions\StreamException;
use Shays\Logger\Formatters\Format;
use Shays\Logger\Handlers\ErrorHandler;
use Shays\Logger\Handlers\ExceptionHandler;
use Shays\Logger\Handlers\LogHandlerInterface;
use Shays\Logger\Log;
use Shays\Logger\LogInterface;
use Shays\Logger\LogLevel;
use Shays\Logger\Serializer\Serializer;
use Shays\Logger\Serializer\SerializerInterface;
use Shays\Logger\Transformers\ContextExceptionTransformer;
use Shays\Logger\Stream\StreamInterface;

class Logger implements LoggerContract
{
	/** @var string Channel name */
	private $channel;

	/** @var DateTimeZone Timezone */
	private $timezone;

	/** @var SerializerInterface Log serializer  */
	private $serializer;

	/** @var string A custom log class (used for customising the log data) */
	private $customLog;

	/** @var LogInterface[] Log entries stored in the request cycle */
	private $logs = [];

	/** @var LogHandlerInterface[] Custom log handlers */
	private $handlers = [];

	/** @var StreamInterface[] Log streams */
	private $streams = [];

	/** @var mixed[] Global context that would be added to logs */
	private $context = [];

	/**
	 * Logger constructor.
	 *
	 * @param string $channel Channel name of the logger instance
	 * @param SerializerInterface|null $serializer A custom log serializer
	 * @param string $customLogClass
	 * @throws CustomLogException
	 */
	public function __construct(
		string $channel,
		?SerializerInterface $serializer = null,
		?string $customLogClass = null
	) {
		$this->channel = $channel;
		$this->serializer = $serializer ?? new Serializer(Format::JSON);
		$this->timezone = new DateTimeZone(date_default_timezone_get());

		// Do we have a custom log class?
		if ($customLogClass) {
			if (!is_subclass_of($customLogClass, LogInterface::class)) {
				throw new CustomLogException('Custom log must inherit ' . Log::class);
			}

			$this->customLog = $customLogClass;
		}

		// Todo set a condition for these handlers
		new ExceptionHandler($this);
		new ErrorHandler($this);
	}

	/**
	 * Set the timezone of the logger
	 *
	 * @param DateTimeZone $timezone
	 * @return LoggerContract
	 */
	public function setTimezone(DateTimeZone $timezone): LoggerContract
	{
		$this->timezone = $timezone;

		return $this;
	}

	/**
	 * Gets the logger timezone used
	 *
	 * @return DateTimeZone
	 */
	public function getTimezone(): DateTimeZone
	{
		return $this->timezone;
	}

	/**
	 * Add a logger handler instance
	 *
	 * @param LogHandlerInterface $handler
	 * @return LoggerContract
	 * @throws HandlerException
	 */
	public function addHandler(LogHandlerInterface $handler): LoggerContract
	{
		if (array_key_exists(get_class($handler), $this->handlers)) {
			throw new HandlerException('Handler ' . get_class($handler) . ' already exists');
		}

		$this->handlers[get_class($handler)] = $handler;

		return $this;
	}

	/**
	 * Add a stream to save new log entries
	 *
	 * @param StreamInterface $fileStream
	 * @return LoggerContract
	 * @throws StreamException
	 */
	public function addStream(StreamInterface $fileStream): LoggerContract
	{
		if (array_key_exists(get_class($fileStream), $this->streams)) {
			throw new StreamException('Stream ' . get_class($fileStream) . ' already exists');
		}

		$this->streams[get_class($fileStream)] = $fileStream;

		return $this;
	}

	/**
	 * Add a global context for the logger instance
	 *
	 * @param mixed[] $context
	 * @return LoggerContract
	 */
	public function addContext(array $context): LoggerContract
	{
		$this->context = array_merge($this->context, ContextExceptionTransformer::transform($context));

		return $this;
	}

	/**
	 * Add a dynamic log level which could be called dynamically with the __call magic method.
	 *
	 * @param int $value Log level value
	 * @param string $levelName Log level name
	 * @return LoggerContract
	 */
	public function addLogLevel(int $value, string $levelName): LoggerContract
	{
		LogLevel::addLevel($value, $levelName);

		return $this;
	}

	/**
	 * Create and handle a log entry with the given data, which
	 * would be then passed to all defined log handlers/steamers
	 *
	 * @param int $level The log entry level (@see LogLevel)
	 * @param string $message The log message
	 * @param array $context Additional log related information
	 * @throws LogException
	 */
	private function createLog(int $level, $message, array $context = []): void
	{
		// Check for message type here so we could control the type error message
		if (gettype($message) !== 'string') {
			throw new LogException('Log message must only contain a string');
		}

		if (! in_array($level, array_keys(LogLevel::getLogLevels()), true)) {
			throw new LogLevelException("Invalid log level: $level");
		}

		$logData = [
			'channel' => $this->channel,
			'level' => $level,
			'message' => $message,
			'context' => array_merge(ContextExceptionTransformer::transform($context), $this->context),
			'time' => new DateTimeImmutable('now', $this->timezone),
		];

		// Create a new log entry
		$log = $this->customLog
			? new $this->customLog(...array_values($logData))
			: new Log(...array_values($logData));

		// Add it for the record
		$this->logs[] = $log;

		// Avoid crashing the app if one of the custom handlers/streamers is failing handling the log
		try {
			// Write to any files added we should write to
			foreach ($this->streams as $stream) {
				if ($stream->shouldWrite($log)) {
					$stream->write($this->serializer->serialize($log));
				}
			}

			// Add any custom handlers we should handle
			foreach ($this->handlers as $handler) {
				if ($handler->shouldHandle($log)) {
					$handler->handle($log);
				}
			}
		} catch (\Throwable $e) {
			// TODO: find a solution to remove failed handlers so we could
			// log the error without potentially ending in an infinite loop
		}
	}

	/**
	 * Gets all the log entries added
	 *
	 * @return LogInterface[]
	 */
	public function getLogs(): array
	{
		return $this->logs;
	}

	/**
	 * Allows dynamic log level invocation
	 *
	 * by adding a custom log level, it is done possible to use
	 * the logger to directly call that level and it would still
	 * be handled by all handlers and streamers.
	 *
	 * @see self::addLogLevel
	 * @param string $name
	 * @param mixed[]|null $args
	 * @throws InvalidDynamicInvocationException
	 */
	public function __call($name, $args): void
	{
		// Can we log a dynamic level?
		if (LogLevel::hasLevelName($name)) {
			// Fill up the context if wasn't added
			$args[1] = $args[1] ?? [];
			[$message, $context] = $args;
			// Check if we get a message
			if (gettype($message) === 'string' && strlen($message) > 0) {
				// Let's handle the log!
				$this->log(LogLevel::getLevelByName($name), $message, $context);
			}

			return;
		}

		throw new InvalidDynamicInvocationException('No log level found. Please make sure you have added'
			. 'the log level before calling it (refer to the README.md for examples)');
	}

	/**
	 * {@inheritDoc}
	 */
	public function log($level, $message, array $context = [])
	{
		$this->createLog($level, $message, $context);
	}

	/**
	 * {@inheritDoc}
	 */
	public function debug($message, array $context = [])
	{
		$this->createLog(LogLevel::DEBUG, $message, $context);
	}

	/**
	 * {@inheritDoc}
	 */
	public function info($message, array $context = [])
	{
		$this->createLog(LogLevel::INFO, $message, $context);
	}

	/**
	 * {@inheritDoc}
	 */
	public function notice($message, array $context = [])
	{
		$this->createLog(LogLevel::NOTICE, $message, $context);
	}

	/**
	 * {@inheritDoc}
	 */
	public function warning($message, array $context = [])
	{
		$this->createLog(LogLevel::WARNING, $message, $context);
	}

	/**
	 * {@inheritDoc}
	 */
	public function error($message, array $context = [])
	{
		$this->createLog(LogLevel::ERROR, $message, $context);
	}

	/**
	 * {@inheritDoc}
	 */
	public function critical($message, array $context = [])
	{
		$this->createLog(LogLevel::CRITICAL, $message, $context);
	}

	/**
	 * {@inheritDoc}
	 */
	public function alert($message, array $context = [])
	{
		$this->createLog(LogLevel::ALERT, $message, $context);
	}

	/**
	 * {@inheritDoc}
	 */
	public function emergency($message, array $context = [])
	{
		$this->createLog(LogLevel::EMERGENCY, $message, $context);
	}
}
