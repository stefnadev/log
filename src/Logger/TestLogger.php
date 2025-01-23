<?php declare(strict_types=1);

namespace Stefna\Logger\Logger;

use PHPUnit\Framework\Assert;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * @phpstan-type LogRecord array{
 *      level: string,
 *      message: string|\Stringable,
 *      context: array<string, mixed>
 *  }
 */
final class TestLogger extends AbstractLogger
{
	/** @var array<array-key, LogRecord> */
	protected array $buffer = [];

	/**
	 * @inheritdoc
	 * @phpstan-param LogLevel::* $level
	 * @param array<string, mixed> $context
	 */
	public function log($level, string|\Stringable $message, array $context = []): void
	{
		$this->buffer[] = [
			'level' => $level,
			'message' => $message,
			'context' => $context,
		];
	}

	/**
	 * @param int $index
	 * @return false|LogRecord
	 */
	public function getLogAt(int $index): false|array
	{
		if (!isset($this->buffer[$index])) {
			return false;
		}

		return $this->buffer[$index];
	}

	public function assertBufferContainsMessage(string $message, int $expectedCount = 1): void
	{
		$found = 0;
		foreach ($this->buffer as $buffer) {
			if ($buffer['message'] === $message) {
				$found++;
			}
		}

		Assert::assertSame($expectedCount, $found);
	}

	/**
	 * @param \Closure(LogRecord): bool $check
	 */
	public function assertBufferContains(\Closure $check): void
	{
		$found = 0;
		foreach ($this->buffer as $buffer) {
			if ($check($buffer)) {
				$found++;
			}
		}

		Assert::assertTrue($found !== 0);
	}

	public function reset(): void
	{
		$this->buffer = [];
	}

	/**
	 * @return array<array-key, LogRecord>
	 */
	public function getBuffer(): array
	{
		return $this->buffer;
	}
}
