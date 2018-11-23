<?php declare(strict_types=1);

namespace Stefna\Logger;

use Monolog\Handler\HandlerInterface;
use Psr\Log\LoggerInterface;
use Stefna\Logger\Config\ConfigInterface;
use Stefna\Logger\Filters\FilterFactory;
use Stefna\Logger\Logger\CallbackLogger;
use Stefna\Logger\Logger\FilterLogger;
use Stefna\Logger\Processor\ChannelProcessor;
use Stefna\Logger\Wrapper\ChannelWrapper;

class Manager
{
	private const MAIN_LOGGER = '_main';

	/** @var \Monolog\Logger[] */
	private static $monologInstances = [];
	/** @var LoggerInterface[] */
	private static $loggerInstances = [];

	/** @var HandlerInterface[] */
	private $monologHandlers = [];
	/** @var callable[] */
	private $monologProcessors = [];

	private $filterFactory;

	public function __construct(\Monolog\Logger $mainLogger, FilterFactory $filterFactory)
	{
		self::$monologInstances[self::MAIN_LOGGER] = $mainLogger;
		$this->monologProcessors[] = new ChannelProcessor();
		$this->filterFactory = $filterFactory;
	}

	public function pushProcessor(callable $callback, string $channel = null): self
	{
		if ($channel === null) {
			array_unshift($this->monologProcessors, $callback);
			foreach (self::$monologInstances as $name => $instance) {
				$instance->pushProcessor($callback);
			}
		}
		elseif (isset(self::$monologInstances[$channel])) {
			self::$monologInstances[$channel]->pushProcessor($callback);
		}


		return $this;
	}

	public function pushHandler(HandlerInterface $handler, string $channel = null): self
	{
		if ($channel === null) {
			array_unshift($this->monologHandlers, $handler);
			foreach (self::$monologInstances as $name => $instance) {
				$instance->pushHandler($handler);
			}
		}
		elseif (isset(self::$monologInstances[$channel])) {
			self::$monologInstances[$channel]->pushHandler($handler);
		}

		return $this;
	}

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

	public function createLogger(string $channel): LoggerInterface
	{
		return new ChannelWrapper(new CallbackLogger([self::$monologInstances[self::MAIN_LOGGER], 'log']), $channel);
	}

	public function createLoggerFromConfig(ConfigInterface $config): LoggerInterface
	{
		if (count($config->getHandlers()) || count($config->getProcessors())) {
			if (!isset(self::$monologInstances[$config->getName()])) {
				$logger = self::$monologInstances[self::MAIN_LOGGER]->withName($config->getName());

				foreach ($config->getHandlers() as $handler) {
					$logger->pushHandler($handler);
				}
				foreach ($config->getProcessors() as $processor) {
					$logger->pushProcessor($processor);
				}
				self::$monologInstances[$config->getName()] = $logger;
			}
			else {
				$logger = self::$monologInstances[$config->getName()];
			}
		}
		else {
			$logger = $this->createLogger($config->getName());
		}

		if (count($config->getFilters())) {
			$filters = [];
			foreach ($config->getFilters() as $filter) {
				$filters[] = $this->filterFactory->createFilter($filter);
			}

			$logger = new FilterLogger($logger, ...$filters);
		}

		return $logger;
	}
}
