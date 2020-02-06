<?php

declare(strict_types=1);

namespace Tests;

use Shays\Logger\Log;
use Shays\Logger\LogLevel;
use Tests\Mocks\CustomErrorHandlerMock;
use Shays\Logger;
use PHPUnit\Framework\TestCase;

final class LoggerHandlerTest extends TestCase
{
	public function testLoggerWithCustomHandler()
	{
		$callableErrorHandler = $this->getMockBuilder(CustomErrorHandlerMock::class)
			->setMethods(['__invoke', 'handle', 'shouldHandle'])
			->getMock();

		$log = new Log(LogLevel::ERROR, 'Error message', [], new \DateTimeImmutable());

		$callableErrorHandler->expects($this->once())
			->method('shouldHandle')
			->with($log)
			->willReturn(true);

		$callableErrorHandler->expects($this->once())
			->method('handle')
			->with($log);

		$logger = (new Logger('test'))->addHandler($callableErrorHandler);
		$logger->error('Error message');
	}

	public function testLoggerWithCustomHandlerWithContext()
	{
		$callableErrorHandler = $this->getMockBuilder(CustomErrorHandlerMock::class)
			->setMethods(['__invoke', 'handle', 'shouldHandle'])
			->getMock();

		$log = new Log(
			LogLevel::ERROR,
			'Error message',
			['environment' => 'test'],
			new \DateTimeImmutable()
		);

		$callableErrorHandler->expects($this->once())
			->method('shouldHandle')
			->with($log)
			->willReturn(true);

		$callableErrorHandler->expects($this->once())
			->method('handle')
			->with($log);

		$logger = (new Logger('test'))->addHandler($callableErrorHandler);
		$logger->error('Error message', ['environment' => 'test']);
	}

	public function testLoggerWithAnIgnoredCustomHandler()
	{
		$callableErrorHandler = $this->getMockBuilder(CustomErrorHandlerMock::class)
			->setMethods(['__invoke', 'handle', 'shouldHandle'])
			->getMock();

		$log = new Log(LogLevel::WARNING, 'Warning message', [], new \DateTimeImmutable());

		$callableErrorHandler->expects($this->once())
			->method('shouldHandle')
			->with($log)
			->willReturn(false);

		$callableErrorHandler->expects($this->never())
			->method('handle');

		$logger = (new Logger('test'))->addHandler($callableErrorHandler);
		$logger->warning('Warning message');
	}
}
