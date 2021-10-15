<?php declare(strict_types=1);

namespace Stefna\Logger;

use Psr\Log\LoggerInterface;
use Stefna\Logger\Config\ConfigInterface;

interface ManagerInterface
{
	/**
	 * Return main logger class
	 *
	 * We don't enforce return type because it can be different based implementation
	 *
	 * @return mixed&LoggerInterface
	 */
	public function getMainLogger();

	/**
	 * @return static
	 */
	public function pushProcessor(callable $callback, string $channel = null);

	public function getLogger(string $channel, ?ConfigInterface $config = null): LoggerInterface;

	public function createLogger(string $channel): LoggerInterface;

	public function createLoggerFromConfig(ConfigInterface $config): LoggerInterface;
}
