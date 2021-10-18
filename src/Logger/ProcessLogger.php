<?php declare(strict_types=1);

namespace Stefna\Logger\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

/**
 * Used for testing processors
 */
final class ProcessLogger extends AbstractLogger
{
	/** @var LoggerInterface */
	private $logger;
	/** @var callable[] */
	private $processors;

	public function __construct(LoggerInterface $logger, callable ...$processors)
	{
		$this->logger = $logger;
		$this->processors = $processors;
	}

	/**
	 * @inheritdoc
	 */
	public function log($level, $message, array $context = []): void
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
