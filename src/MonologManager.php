<?php declare(strict_types=1);

namespace Stefna\Logger;

use Monolog\Handler\AbstractHandler;
use Monolog\Handler\HandlerInterface;
use Psr\Log\LoggerInterface;
use Stefna\Logger\Config\ConfigInterface;
use Stefna\Logger\Filters\FilterFactory;
use Stefna\Logger\Filters\FilterInterface;
use Stefna\Logger\Filters\LogLevelRangeFilter;
use Stefna\Logger\Logger\FilterLogger;
use Stefna\Logger\Processor\ChannelProcessor;
use Stefna\Logger\Wrapper\ChannelWrapper;

class MonologManager extends AbstractManager
{
	private const MAIN_LOGGER = '_main';

	/** @var \Monolog\Logger[] */
	private static array $monologInstances = [];

	/** @var HandlerInterface[] */
	private array $monologHandlers = [];
	/** @var callable[] */
	private array $monologProcessors = [];

	public function __construct(
		\Monolog\Logger $mainLogger,
		private readonly FilterFactory $filterFactory,
	) {
		self::$monologInstances[self::MAIN_LOGGER] = $mainLogger;
		$this->monologProcessors[] = new ChannelProcessor();
	}

	public function getMainLogger(): \Monolog\Logger
	{
		return self::$monologInstances[self::MAIN_LOGGER];
	}

	public function pushProcessor(callable $callback, string $channel = null): static
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

	public function pushHandler(HandlerInterface $handler, string $channel = null): static
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

	public function createLogger(string $channel): LoggerInterface
	{
		return new ChannelWrapper(self::$monologInstances[self::MAIN_LOGGER], $channel);
	}

	public function createLoggerFromConfig(ConfigInterface $config): LoggerInterface
	{
		$specialLogLevel = null;
		$filters = [];
		foreach ($config->getFilters() as $filter) {
			if ($filter instanceof FilterInterface) {
				$filters[] = $filter;
				continue;
			}
			$filterInstance = $this->filterFactory->createFilter($filter);
			if ($filterInstance instanceof LogLevelRangeFilter) {
				$specialLogLevel = $filterInstance->getMinLevel();
			}
			if ($filterInstance instanceof FilterInterface) {
				$filters[] = $filterInstance;
			}
		}

		if ($specialLogLevel !== null || count($config->getHandlers()) || count($config->getProcessors())) {
			if (!isset(self::$monologInstances[$config->getName()])) {
				$logger = self::$monologInstances[self::MAIN_LOGGER]->withName($config->getName());

				if ($specialLogLevel !== null) {
					$handlers = $logger->getHandlers();
					$mainHandler = end($handlers);
					$mainKey = key($handlers);
					if ($mainHandler && method_exists($mainHandler, 'setLevel')) {
						// We assume the first handler is the main handler and that's fine to clone and modify it
						// We don't want to modify the original handler because we shouldn't change the log level for
						// entire application

						/** @var AbstractHandler $newHandler */
						$newHandler = clone $mainHandler;
						$newHandler->setLevel($specialLogLevel);
						$handlers[$mainKey] = $newHandler;
						$logger->setHandlers($handlers);
					}
				}

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

		if (count($filters)) {
			$logger = new FilterLogger($logger, ...$filters);
		}

		return $logger;
	}
}
