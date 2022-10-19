<?php declare(strict_types=1);

namespace Stefna\Logger\Filter;

use Monolog\Level;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Stefna\Logger\Filters\LogLevelRangeFilter;

final class LogLevelRangeFilterTest extends TestCase
{
	/**
	 * @dataProvider levels
	 */
	public function test(Level $min, Level $max, array $levels, bool $expectedResult): void
	{
		$filter = new LogLevelRangeFilter($min, $max);

		foreach ($levels as $level) {
			$this->assertSame($expectedResult, $filter($level, ''));
		}
	}

	public function levels()
	{
		return [
			[Level::Debug, Level::Debug, [LogLevel::DEBUG], true],
			[Level::Debug, Level::Debug, [
				LogLevel::EMERGENCY,
				LogLevel::ALERT,
				LogLevel::CRITICAL,
				LogLevel::ERROR,
				LogLevel::WARNING,
				LogLevel::NOTICE,
				LogLevel::INFO,
			], false],
			[Level::Debug, Level::Emergency, [
				LogLevel::DEBUG,
				LogLevel::EMERGENCY,
				LogLevel::ALERT,
				LogLevel::CRITICAL,
				LogLevel::ERROR,
				LogLevel::WARNING,
				LogLevel::NOTICE,
				LogLevel::INFO,
			], true],
			[Level::Notice, Level::Alert, [
				LogLevel::ALERT,
				LogLevel::CRITICAL,
				LogLevel::ERROR,
				LogLevel::WARNING,
				LogLevel::NOTICE,
			], true],
			[Level::Notice, Level::Alert, [
				LogLevel::DEBUG,
				LogLevel::EMERGENCY,
				LogLevel::INFO,
			], false],
		];
	}
}
