<?php

declare(strict_types=1);

namespace Shays;

use DateTimeImmutable;
use DateTimeZone;
use Shays\Contracts\LoggerContract;
use Shays\Exceptions\FormatException;
use Shays\Exceptions\HandlerException;
use Shays\Exceptions\LogLevelException;
use Shays\Exceptions\StreamException;
use Shays\Formatters\Format;
use Shays\Handlers\ErrorHandler;
use Shays\Handlers\ExceptionHandler;
use Shays\Handlers\LogHandlerInterface;
use Shays\Logger\Log;
use Shays\Logger\LogInterface;
use Shays\Logger\LogLevel;
use Shays\Serializer\Serializer;
use Shays\Serializer\SerializerInterface;
use Shays\Transformers\ContextExceptionTransformer;
use Shays\Stream\StreamInterface;

class Logger implements LoggerContract
{
	/** @var string Channel name */
	private $channel;

	/** @var DateTimeZone Timezone */
	private $timezone;

	/** @var SerializerInterface Log serializer  */
	private $serializer;

	/** @var LogInterface The log object */
	private $log;

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
	 * @param LogInterface|null $log
	 */
	public function __construct(string $channel, ?SerializerInterface $serializer = null, ?LogInterface $log = null)
	{
		$this->channel = $channel;
		$this->serializer = $serializer ?? new Serializer(Format::JSON);
		$this->log = $log ?? Log::class;
		$this->timezone = new DateTimeZone(date_default_timezone_get());

		// Todo set a condition for these handlers
		new ExceptionHandler($this);
		new ErrorHandler($this);
	}

	/**
	 * Sets the logger format handler
	 *
	 * @param string $format
	 * @return LoggerContract
	 * @throws FormatException
	 */
	public function setFormat(string $format): LoggerContract
	{
		// TODO add additional formats and serializers
		if (! in_array(strtolower($format), Format::getSupportedFormats(), true)) {
			throw new FormatException("Format {$format} is not supported");
		}

		return $this;
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
	 * Gets all the log levels
	 *
	 * @return mixed[]
	 */
	protected function getLogLevels(): array
	{
		return LogLevel::getLogLevels();
	}

	/**
	 * Gets the log level name of the level provided
	 *
	 * @param int $level
	 * @return string
	 */
	protected function getLevelName(int $level): string
	{
		return LogLevel::getLevelName($level);
	}

	/**
	 * {@inheritDoc}
	 */
	public function createLog(int $level, string $message, array $context = []): void
	{
		if (! in_array($level, $this->getLogLevels(), true)) {
			throw new LogLevelException("Invalid log level: $level");
		}

		// Create a new log entry
		$log = new $this->log(
			$level,
			$message,
			array_merge(ContextExceptionTransformer::transform($context), $this->context),
			new DateTimeImmutable('now', $this->timezone)
		);

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
	 * Get the last log entry
	 *
	 * @return LogInterface|null
	 */
	public function getLast(): ?LogInterface
	{
		$logs = $this->getLogs();
		if (count($logs) >= 1) {
			return end($logs);
		}

		return null;
	}

	/**
	 * Gets all the log entries added
	 *
	 * @return mixed[]
	 */
	public function getLogs(): array
	{
		return $this->logs;
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
