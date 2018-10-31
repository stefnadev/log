<?php declare(strict_types=1);

namespace Stefna\Logger\Logger;

use Psr\Log\AbstractLogger;

class CallbackLogger extends AbstractLogger
{
	/**
	 * @var callable
	 */
	private $logger;

	public function __construct(callable $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * @inheritdoc
	 */
	public function log($level, $message, array $context = array()): void
	{
		$logger = $this->logger;
		$logger($level, $message, $context);
	}
}
