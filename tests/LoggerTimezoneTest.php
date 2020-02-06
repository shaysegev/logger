<?php

declare(strict_types=1);

namespace Tests;

use Shays\Logger;
use PHPUnit\Framework\TestCase;

final class LoggerTimezoneTest extends TestCase
{
	public function testLoggerWithDefaultTimezone()
	{
		$logger = new Logger('test');
		$this->assertEquals(
			$logger->getTimezone()->getName(),
			date_default_timezone_get()
		);
	}

	public function testLoggerWithValidTimezone()
	{
		$logger = (new Logger('test'))->setTimezone(new \DateTimeZone('Europe/London'));
		$this->assertEquals(
			$logger->getTimezone()->getName(),
			'Europe/London'
		);
	}

	public function testLoggerWithInvalidTimezone()
	{
		$this->expectException(\Throwable::class);
		(new Logger('test'))->setTimezone(new \DateTimeZone('Europe/Londoa'));
	}
}
