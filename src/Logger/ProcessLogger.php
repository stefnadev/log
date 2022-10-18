<?php declare(strict_types=1);

namespace Stefna\Logger\Logger;

use Monolog\Level;
use Monolog\LogRecord;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

/**
 * Used for testing processors
 */
final class ProcessLogger extends AbstractLogger
{
	/** @var callable[] */
	private array $processors;

	public function __construct(
		private readonly LoggerInterface $logger,
		callable ...$processors
	) {
		$this->processors = $processors;
	}

	/**
	 * @inheritdoc
	 */
	public function log($level, string|\Stringable $message, array $context = []): void
	{
		$record = [
			'level' => $level,
			'message' => $message,
			'context' => $context,
		];
		foreach ($this->processors as $processor) {
			$record = $processor($record);
		}
		$this->logger->log($record['level'], $record['message'], $record['context']);
	}
}
