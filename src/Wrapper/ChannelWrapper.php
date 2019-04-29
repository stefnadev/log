<?php declare(strict_types=1);

namespace Stefna\Logger\Wrapper;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class ChannelWrapper extends AbstractLogger
{
	/** @var LoggerInterface */
	private $logger;
	/** @var string */
	private $channel;

	public function __construct(LoggerInterface $logger, string $channel)
	{
		$this->logger = $logger;
		$this->channel = $channel;
	}

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
		$context['channel'] = $this->channel;
		$this->logger->log($level, $message, $context);
	}
}
