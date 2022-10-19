<?php declare(strict_types=1);

namespace Stefna\Logger\Filter;

use Monolog\Level;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Stefna\Logger\Filters\ActivateLevelFilter;

final class ActivateLevelFilterTest extends TestCase
{
	public function test(): void
	{
		$filter = new ActivateLevelFilter(Level::Error);

		$this->assertFalse($filter(LogLevel::INFO, '', []));
		$this->assertTrue($filter(LogLevel::ERROR, '', []));
		$this->assertTrue($filter(LogLevel::INFO, '', []));
	}
}
