<?php declare(strict_types=1);

namespace Stefna\Logger\Wrapper;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class ChainWrapper extends AbstractLogger
{
	/** @var LoggerInterface[] */
	private array $loggers;

	public function __construct(LoggerInterface ...$loggers)
	{
		$this->loggers = $loggers;
	}

	public function addLogger(LoggerInterface $logger): void
	{
		$this->loggers[] = $logger;
	}

	public function log($level, string|\Stringable $message, array $context = []): void
	{
		foreach ($this->loggers as $logger) {
			$logger->log($level, $message, $context);
		}
	}
}
