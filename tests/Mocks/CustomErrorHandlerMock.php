<?php

declare(strict_types=1);

namespace Tests\Mocks;

use Shays\Logger\Handlers\LogHandlerInterface;
use Shays\Logger\LogInterface;

class CustomErrorHandlerMock implements LogHandlerInterface
{
	public $handler;

	public function shouldHandle(LogInterface $log): bool
	{
		// is mocked
		return true;
	}

	public function handle(LogInterface $log): void
	{
		// is mocked
	}
}
