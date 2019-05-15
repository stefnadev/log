<?php declare(strict_types=1);

namespace Stefna\Logger\Logger;

use Psr\Log\AbstractLogger;

class BufferLogger extends AbstractLogger
{
	protected $buffer = [];

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed $level
	 * @param string $message
	 * @param array $context
	 *
	 * @return void
	 */
	public function log($level, $message, array $context = []): void
	{
		$this->buffer[] = [$level, $message, $context];
	}

	public function getBuffer(): array
	{
		return $this->buffer;
	}
}
