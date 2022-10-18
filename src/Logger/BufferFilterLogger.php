<?php declare(strict_types=1);

namespace Stefna\Logger\Logger;

use Psr\Log\LoggerInterface;
use Stefna\Logger\Filters\FilterInterface;

class BufferFilterLogger extends BufferLogger
{
	/** @var FilterInterface[] */
	private array $filters;

	public function __construct(
		private readonly LoggerInterface $logger,
		FilterInterface ...$filters
	) {
		$this->filters = $filters;
	}

	/**
	 * @inheritdoc
	 */
	public function log($level, string|\Stringable $message, array $context = []): void
	{
		parent::log($level, $message, $context);

		foreach ($this->filters as $filter) {
			if (!$filter($level, $message, $context)) {
				return;
			}
		}

		foreach ($this->buffer as [$rowLevel, $rowMessage, $rowContext]) {
			$this->logger->log($rowLevel, $rowMessage, $rowContext);
		}
		$this->buffer = [];
	}
}
