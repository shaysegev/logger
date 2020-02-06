<?php

declare(strict_types=1);

namespace Tests\Mocks;

use Shays\Logger\LogInterface;
use Shays\Logger\Stream\StreamInterface;

class DatabaseStreamMock implements StreamInterface
{
	public function shouldWrite(LogInterface $log): bool
	{
		// is mocked
		return true;
	}

	public function write(string $log): void
	{
		// is mocked
	}
}
