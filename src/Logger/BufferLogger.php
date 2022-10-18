<?php declare(strict_types=1);

namespace Stefna\Logger\Logger;

use Psr\Log\AbstractLogger;

class BufferLogger extends AbstractLogger
{
	/** @var array<array-key, array{0: string, 1: string, 2: array<string, mixed>}> */
	protected array $buffer = [];

	/**
	 * @inheritdoc
	 */
	public function log($level, string|\Stringable $message, array $context = []): void
	{
		$this->buffer[] = [$level, $message, $context];
	}

	/**
	 * @return array<array-key, array{0: string, 1: string, 2: array<string, mixed>}>
	 */
	public function getBuffer(): array
	{
		return $this->buffer;
	}
}
