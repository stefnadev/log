<?php declare(strict_types=1);

namespace Stefna\Logger\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Stefna\Logger\Filters\FilterInterface;

class FilterLogger extends AbstractLogger
{
	/** @var LoggerInterface */
	private $logger;
	/** @var FilterInterface[] */
	private $filters;

	public function __construct(LoggerInterface $logger, FilterInterface ...$filters)
	{
		$this->logger = $logger;
		$this->filters = $filters;
	}

	/**
	 * @inheritdoc
	 */
	public function log($level, $message, array $context = [])
	{
		foreach ($this->filters as $filter) {
			if (!$filter($level, $message, $context)) {
				return;
			}
		}

		$this->logger->log($level, $message, $context);
	}
}
