<?php declare(strict_types=1);

namespace Stefna\Logger\Logger;

use Psr\Log\AbstractLogger;

class CallbackLogger extends AbstractLogger
{
	/** @var callable */
	private $logger;

	/**
	 * @phpstan-param callable(string, string|\Stringable, array<mixed>): void $logger
	 */
	public function __construct(callable $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * @inheritdoc
	 * @param array<mixed> $context
	 */
	public function log($level, string|\Stringable $message, array $context = []): void
	{
		$logger = $this->logger;
		$logger($level, $message, $context);
	}
}
