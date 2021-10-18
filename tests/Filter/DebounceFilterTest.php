<?php declare(strict_types=1);

namespace Stefna\Logger\Filter;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Stefna\Logger\Filters\DebounceFilter;
use Stefna\Logger\Logger\FilterLogger;

final class DebounceFilterTest extends TestCase
{
	public function testDebounce(): void
	{
		$interval = new \DateInterval('PT1H');

		$debounceFilter = new DebounceFilter(function($level, $message, $context) use ($interval) {
			DebounceFilterTest::assertArrayHasKey(DebounceFilter::DEBOUNCE_INTERVAL, $context);
			DebounceFilterTest::assertSame($interval, $context[DebounceFilter::DEBOUNCE_INTERVAL]);
			return true;
		});

		$logger = new FilterLogger(new NullLogger(), $debounceFilter);
		$logger->alert('Db connect error', [
			DebounceFilter::DEBOUNCE_INTERVAL => $interval,
		]);
	}

	public function testConvertOfStringInterval(): void
	{
		$strInterval = 'PT2H';
		$debounceFilter = new DebounceFilter(function($level, $message, $context) use ($strInterval) {
			DebounceFilterTest::assertArrayHasKey(DebounceFilter::DEBOUNCE_INTERVAL, $context);
			DebounceFilterTest::isInstanceOf(\DateInterval::class, $context[DebounceFilter::DEBOUNCE_INTERVAL]);
			return true;
		});

		$logger = new FilterLogger(new NullLogger(), $debounceFilter);
		$logger->alert('Db connect error', [
			DebounceFilter::DEBOUNCE_INTERVAL => $strInterval,
		]);
	}

	public function testInvalidInterval(): void
	{
		$strInterval = 'Invalid interval';
		$debounceFilter = new DebounceFilter(function($level, $message, $context) use ($strInterval) {
			return true;
		});

		$this->expectException(\Exception::class);

		$logger = new FilterLogger(new NullLogger(), $debounceFilter);
		$logger->alert('Db connect error', [
			DebounceFilter::DEBOUNCE_INTERVAL => $strInterval,
		]);
	}

	public function testNotExecutedWithoutInterval(): void
	{
		$debounceFilter = new DebounceFilter(function() {
			DebounceFilterTest::fail('Shouldn\'t execute');
		});

		$logger = new FilterLogger(new NullLogger(), $debounceFilter);
		$logger->alert('Db connect error');

		$this->assertTrue(true);
	}
}
