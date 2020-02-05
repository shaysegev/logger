<?php

declare(strict_types=1);

namespace Tests;

use Shays\Logger;
use PHPUnit\Framework\TestCase;

final class LoggerBuilderTest extends TestCase
{
    public function testBuildWithDefaultTimezone()
    {
        $logger = new Logger('test');
        $this->assertEquals(
            $logger->getTimezone()->getTimezone()->getName(),
            date_default_timezone_get()
        );
    }

    public function testBuildWithValidTimezone()
    {
        $logger = (new Logger('test'))->setTimezone('Europe/London');
        $this->assertEquals(
            $logger->getTimezone()->getTimezone()->getName(),
             'Europe/London'
        );
    }

    public function testBuildWithInvalidTimezone()
    {
        $this->expectException(\Exception::class);
        $logger = (new Logger('test'))->setTimezone('Europe/Londoa');
    }
}
