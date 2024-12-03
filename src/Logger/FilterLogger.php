<?php declare(strict_types=1);

namespace Stefna\Logger\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Stefna\Logger\Filters\FilterInterface;

class FilterLogger extends AbstractLogger
{
	/** @var FilterInterface[] */
	private array $filters;

	public function __construct(
		private readonly LoggerInterface $logger,
		FilterInterface ...$filters,
	) {
		$this->filters = $filters;
	}

	/**
	 * @inheritdoc
	 * @param array<mixed> $context
	 */
	public function log($level, string|\Stringable $message, array $context = []): void
	{
		foreach ($this->filters as $filter) {
			if (!$filter($level, $message, $context)) {
				return;
			}
		}

		$this->logger->log($level, $message, $context);
	}
}
