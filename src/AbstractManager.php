<?php declare(strict_types=1);

namespace Stefna\Logger;

use Psr\Log\LoggerInterface;
use Stefna\Logger\Config\ConfigInterface;

abstract class AbstractManager implements ManagerInterface
{
	/** @var LoggerInterface[] */
	protected static array $loggerInstances = [];

	public function getLogger(string $channel, ?ConfigInterface $config = null): LoggerInterface
	{
		if (!isset(self::$loggerInstances[$channel])) {
			if ($config) {
				self::$loggerInstances[$channel] = $this->createLoggerFromConfig($config);
			}
			else {
				self::$loggerInstances[$channel] = $this->createLogger($channel);
			}
		}

		return self::$loggerInstances[$channel];
	}
}
