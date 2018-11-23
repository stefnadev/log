<?php declare(strict_types=1);

namespace Stefna\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Stefna\Logger\Config\ConfigInterface;
use Stefna\Logger\Exceptions\ConfigurationNotDefined;
use Stefna\Logger\Exceptions\ManagerNotDefined;

class Logger
{
	/** @var Manager */
	private static $manager;
	private static $config = [];

	public static function setManager(Manager $manager): void
	{
		self::$manager = $manager;
	}

	public static function setChannelConfig(string $channel, ConfigInterface $config): void
	{
		self::$config[$channel] = $config;
	}

	public static function getChannelConfig(string $channel)
	{
		if (isset(self::$config[$channel])) {
			return self::$config[$channel];
		}

		throw new ConfigurationNotDefined($channel);
	}

	public static function setGlobalConfig(ConfigInterface ...$configs): void
	{
		foreach ($configs as $config) {
			self::setChannelConfig($config->getName(), $config);
		}
	}

	public static function getManager(): Manager
	{
		if (self::$manager === null) {
			throw new ManagerNotDefined('');
		}
		return self::$manager;
	}
	
	public static function getLogger(string $name): LoggerInterface
	{
		if (self::$manager === null) {
			//todo maybe change this to an exception?
			return new NullLogger();
		}

		return self::$manager->getLogger($name, self::$config[$name]??null);
	}
}
