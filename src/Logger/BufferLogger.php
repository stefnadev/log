<?php declare(strict_types=1);

namespace Stefna\Logger\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class BufferLogger extends AbstractLogger
{
	/** @var array<array-key, array{0: string, 1: string|\Stringable, 2: array<string, mixed>}> */
	protected array $buffer = [];

	/**
	 * @inheritdoc
	 * @phpstan-param LogLevel::* $level
	 * @param array<string, mixed> $context
	 */
	public function log($level, string|\Stringable $message, array $context = []): void
	{
		$this->buffer[] = [$level, $message, $context];
	}

	/**
	 * @return array<array-key, array{0: string, 1: string|\Stringable, 2: array<string, mixed>}>
	 */
	public function getBuffer(): array
	{
		return $this->buffer;
	}
}
