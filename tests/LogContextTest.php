<?php

declare(strict_types=1);

namespace Tests;

use Shays\Logger;
use PHPUnit\Framework\TestCase;

final class LogContextTest extends TestCase
{
	public function testLoggingGlobalContext(): void
	{
		$logger = new Logger('test');
		$logger->addContext([
			'userId' => 3,
		]);

		$logger->info('info message');

		$log = $logger->getLogs()[0];
		$this->assertEquals($log->hasContext('userId'), true);
		$this->assertEquals($log->getContext('userId'), 3);
		$this->assertEquals($log->get('userId'), 3);
		$this->assertEquals($log->getAllContext()['userId'], 3);
	}

	public function testLoggingGlobalContextAndLogContext(): void
	{
		$logger = new Logger('test');
		$logger->addContext([
			'userId' => 3,
		]);

		$logger->info('info message', ['userType' => 'admin']);

		$log = $logger->getLogs()[0];
		$this->assertEquals($log->hasContext('userId'), true);
		$this->assertEquals($log->getContext('userId'), 3);

		$this->assertEquals($log->hasContext('userType'), true);
		$this->assertEquals($log->getContext('userType'), 'admin');

		$this->assertEquals($log->getAllContext(), [
			'userId' => 3,
			'userType' => 'admin',
		]);

		// append some data and make sure only global context remains
		$logger->info('info message');

		$log = $logger->getLogs()[1];
		$this->assertEquals($log->hasContext('userId'), true);
		$this->assertEquals($log->getContext('userId'), 3);

		$this->assertEquals($log->hasContext('userType'), false);
		$this->assertEquals($log->getContext('userType'), null);

		$this->assertEquals($log->getAllContext(), [
			'userId' => 3,
		]);
	}
}
