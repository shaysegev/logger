<?php

declare(strict_types=1);

namespace Tests;

use InvalidArgumentException;
use Shays\Logger;
use Shays\Logger\LogLevel;
use PHPUnit\Framework\TestCase;

final class LevelTest extends TestCase
{
    public function testLogDebug()
    {
        $logger = new Logger('test');
        $logger->debug('debug message');

        $this->assertEquals($logger->getLast()->getMessage(), 'debug message');
        $this->assertEquals($logger->getLast()->getLevel(), 100);
        $this->assertEquals($logger->getLast()->getLevelName(), 'DEBUG');
    }

    public function testLogInfo()
    {
        $logger = new Logger('test');
        $logger->info('info message');

        $this->assertEquals($logger->getLast()->getMessage(), 'info message');
        $this->assertEquals($logger->getLast()->getLevel(), 200);
        $this->assertEquals($logger->getLast()->getLevelName(), 'INFO');
    }

    public function testLogNotice()
    {
        $logger = new Logger('test');
        $logger->notice('notice message');

        $this->assertEquals($logger->getLast()->getMessage(), 'notice message');
        $this->assertEquals($logger->getLast()->getLevel(), 300);
        $this->assertEquals($logger->getLast()->getLevelName(), 'NOTICE');
    }

    public function testLogWarning()
    {
        $logger = new Logger('test');
        $logger->warning('warning message');

        $this->assertEquals($logger->getLast()->getMessage(), 'warning message');
        $this->assertEquals($logger->getLast()->getLevel(), 400);
        $this->assertEquals($logger->getLast()->getLevelName(), 'WARNING');
    }

    public function testLogError()
    {
        $logger = new Logger('test');
        $logger->error('error message');

        $this->assertEquals($logger->getLast()->getMessage(), 'error message');
        $this->assertEquals($logger->getLast()->getLevel(), 500);
        $this->assertEquals($logger->getLast()->getLevelName(), 'ERROR');
    }

    public function testLogCritical()
    {
        $logger = new Logger('test');
        $logger->critical('critical message');

        $this->assertEquals($logger->getLast()->getMessage(), 'critical message');
        $this->assertEquals($logger->getLast()->getLevel(), 600);
        $this->assertEquals($logger->getLast()->getLevelName(), 'CRITICAL');
    }

    public function testLogAlert()
    {
        $logger = new Logger('test');
        $logger->alert('alert message');

        $this->assertEquals($logger->getLast()->getMessage(), 'alert message');
        $this->assertEquals($logger->getLast()->getLevel(), 700);
        $this->assertEquals($logger->getLast()->getLevelName(), 'ALERT');
    }

    public function testLogEmergency()
    {
        $logger = new Logger('test');
        $logger->emergency('emergency message');

        $this->assertEquals($logger->getLast()->getMessage(), 'emergency message');
        $this->assertEquals($logger->getLast()->getLevel(), 800);
        $this->assertEquals($logger->getLast()->getLevelName(), 'EMERGENCY');
    }

    public function testLogUnknownLevel()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid log level: unknown');

        $logger = new Logger('test');
        $logger->log('unknown', 'unknown level message');
    }
}
