<?php

declare(strict_types=1);

namespace Tests;

use Shays\Logger\Formatters\Format;
use Shays\Logger\Log;
use Shays\Logger\LogLevel;
use Shays\Logger\Serializer\Serializer;
use Tests\Mocks\DatabaseStreamMock;
use Shays\Logger;
use PHPUnit\Framework\TestCase;
use Tests\Mocks\XmlEncoderMock;

final class LoggerStreamTest extends TestCase
{
	public function testLoggerWithCustomStream(): void
	{
		$callableDatabaseStreamer = $this->getMockBuilder(DatabaseStreamMock::class)
			->setMethods(['__invoke', 'write', 'shouldWrite'])
			->getMock();

		$log = new Log('test', LogLevel::ERROR, 'Error message', [], new \DateTimeImmutable());
		$serializer = new Serializer(Format::JSON);

		$callableDatabaseStreamer->expects($this->once())
			->method('shouldWrite')
			->with($log)
			->willReturn(true);

		$callableDatabaseStreamer->expects($this->once())
			->method('write')
			->with($serializer->serialize($log));

		$logger = (new Logger('test'))->addStream($callableDatabaseStreamer);
		$logger->error('Error message');
	}

	public function testLoggerWithCustomStreamAndSerializer(): void
	{
		$callableDatabaseStreamer = $this->getMockBuilder(DatabaseStreamMock::class)
			->setMethods(['__invoke', 'write', 'shouldWrite'])
			->getMock();

		$log = new Log('test', LogLevel::ERROR, 'Error message', [], new \DateTimeImmutable());
		$serializer = new Serializer(Format::JSON);

		$callableDatabaseStreamer->expects($this->once())
			->method('shouldWrite')
			->with($log)
			->willReturn(true);

		$callableDatabaseStreamer->expects($this->once())
			->method('write')
			->with($serializer->serialize($log));

		$logger = (new Logger('test'))->addStream($callableDatabaseStreamer);
		$logger->error('Error message');
	}

	public function testLoggerWithCustomStreamAndCustomSerializer(): void
	{
		$callableDatabaseStreamer = $this->getMockBuilder(DatabaseStreamMock::class)
			->setMethods(['__invoke', 'write', 'shouldWrite'])
			->getMock();

		$log = new Log('test', LogLevel::ERROR, 'Error message', [], new \DateTimeImmutable());

		$encoder = new XmlEncoderMock();

		$callableDatabaseStreamer->expects($this->once())
			->method('shouldWrite')
			->with($log)
			->willReturn(true);

		$callableDatabaseStreamer->expects($this->once())
			->method('write')
			->with($encoder->serialize($log));

		$logger = (new Logger('test', $encoder))->addStream($callableDatabaseStreamer);
		$logger->error('Error message');
	}
}
