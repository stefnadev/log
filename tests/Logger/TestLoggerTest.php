<?php declare(strict_types=1);

namespace Stefna\Logger\Logger;

use PHPUnit\Framework\TestCase;

final class TestLoggerTest extends TestCase
{
	public function testAssert(): void
	{
		$logger = new TestLogger();
		$logger->debug('Test message');
		$logger->info('Info message');
		$logger->info('Info message');
		$logger->error('Error message');

		$logger->assertBufferContainsMessage('Info message', 2);
	}

	public function testCustomCheck(): void
	{
		$logger = new TestLogger();
		$logger->debug('Test message', [
			'test' => 1,
		]);
		$logger->notice('Test message', [
			'test' => 1,
		]);
		$logger->info('Test message', [
			'test' => 1,
		]);
		$logger->error('Test message', [
			'test' => 1,
		]);

		$logger->assertBufferContains(fn (array $record) => $record['context']['test'] === 1);
	}

	public function testReset(): void
	{
		$logger = new TestLogger();
		$logger->debug('Test message');

		$this->assertCount(1, $logger->getBuffer());

		$logger->reset();

		$this->assertCount(0, $logger->getBuffer());
	}
}
