<?php

declare(strict_types=1);

namespace Tests;

use Shays\Logger;
use Shays\Logger\Exceptions\InvalidDynamicInvocationException;
use Shays\Logger\Exceptions\LogLevelException;
use PHPUnit\Framework\TestCase;

final class LogLevelTest extends TestCase
{
	public function testLogDebug(): void
	{
		$logger = new Logger('test');
		$logger->debug('debug message');

		$this->assertEquals($logger->getLogs()[0]->getMessage(), 'debug message');
		$this->assertEquals($logger->getLogs()[0]->getLevel(), 100);
		$this->assertEquals($logger->getLogs()[0]->getLevelName(), 'DEBUG');
	}

	public function testLogInfo(): void
	{
		$logger = new Logger('test');
		$logger->info('info message');

		$this->assertEquals($logger->getLogs()[0]->getMessage(), 'info message');
		$this->assertEquals($logger->getLogs()[0]->getLevel(), 200);
		$this->assertEquals($logger->getLogs()[0]->getLevelName(), 'INFO');
	}

	public function testLogNotice(): void
	{
		$logger = new Logger('test');
		$logger->notice('notice message');

		$this->assertEquals($logger->getLogs()[0]->getMessage(), 'notice message');
		$this->assertEquals($logger->getLogs()[0]->getLevel(), 300);
		$this->assertEquals($logger->getLogs()[0]->getLevelName(), 'NOTICE');
	}

	public function testLogWarning(): void
	{
		$logger = new Logger('test');
		$logger->warning('warning message');

		$this->assertEquals($logger->getLogs()[0]->getMessage(), 'warning message');
		$this->assertEquals($logger->getLogs()[0]->getLevel(), 400);
		$this->assertEquals($logger->getLogs()[0]->getLevelName(), 'WARNING');
	}

	public function testLogError(): void
	{
		$logger = new Logger('test');
		$logger->error('error message');

		$this->assertEquals($logger->getLogs()[0]->getMessage(), 'error message');
		$this->assertEquals($logger->getLogs()[0]->getLevel(), 500);
		$this->assertEquals($logger->getLogs()[0]->getLevelName(), 'ERROR');
	}

	public function testLogCritical(): void
	{
		$logger = new Logger('test');
		$logger->critical('critical message');

		$this->assertEquals($logger->getLogs()[0]->getMessage(), 'critical message');
		$this->assertEquals($logger->getLogs()[0]->getLevel(), 600);
		$this->assertEquals($logger->getLogs()[0]->getLevelName(), 'CRITICAL');
	}

	public function testLogAlert(): void
	{
		$logger = new Logger('test');
		$logger->alert('alert message');

		$this->assertEquals($logger->getLogs()[0]->getMessage(), 'alert message');
		$this->assertEquals($logger->getLogs()[0]->getLevel(), 700);
		$this->assertEquals($logger->getLogs()[0]->getLevelName(), 'ALERT');
	}

	public function testLogEmergency(): void
	{
		$logger = new Logger('test');
		$logger->emergency('emergency message');

		$this->assertEquals($logger->getLogs()[0]->getMessage(), 'emergency message');
		$this->assertEquals($logger->getLogs()[0]->getLevel(), 800);
		$this->assertEquals($logger->getLogs()[0]->getLevelName(), 'EMERGENCY');
	}

	public function testLogUnknownLevel(): void
	{
		$this->expectException(LogLevelException::class);
		$this->expectExceptionMessage('Invalid log level: 350');

		$logger = new Logger('test');
		$logger->log(350, 'unknown level message');
	}

	public function testAddNewLogLevel(): void
	{
		$logger = new Logger('test');
		$logger->addLogLevel(50, 'progress');
		$logger->progress('Progress message');

		$this->assertEquals($logger->getLogs()[0]->getMessage(), 'Progress message');
		$this->assertEquals($logger->getLogs()[0]->getLevel(), 50);
		$this->assertEquals($logger->getLogs()[0]->getLevelName(), 'PROGRESS');
	}

	public function testAddNewLogLevelThatAlreadyExists(): void
	{
		$this->expectException(LogLevelException::class);
		$this->expectExceptionMessage('Log level 200 already exists. Level: INFO');

		$logger = new Logger('test');
		$logger->addLogLevel(200, 'progress');
	}

	public function testCallAnUnknownLogLevel(): void
	{
		$this->expectException(InvalidDynamicInvocationException::class);
		$logger = new Logger('test');
		$logger->dangerous('Something dangerous has happened!');
	}
}
