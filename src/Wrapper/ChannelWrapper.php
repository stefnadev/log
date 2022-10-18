<?php declare(strict_types=1);

namespace Stefna\Logger\Wrapper;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class ChannelWrapper extends AbstractLogger
{
	public function __construct(
		private readonly LoggerInterface $logger,
		private readonly string $channel,
	) {}

	public function log($level, string|\Stringable $message, array $context = []): void
	{
		$context['channel'] = $this->channel;
		$this->logger->log($level, $message, $context);
	}
}
