<?php declare(strict_types=1);

namespace Stefna\Logger\Limiter;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Logger extends AbstractLogger
{
	public const INTERVAL_DAY = 'P1D';
	public const INTERVAL_HOUR = 'PT1H';

	private const LOG_CHANNEL = 'logLimiter';
	private const CACHE_PREFIX = 'logLimiter_';

	/** @var LoggerInterface */
	private $logger;
	/** @var LoggerInterface */
	private $myLogger;
	/** @var string */
	private $interval;
	/** @var int */
	private $now;
	/** @var CacheInterface */
	private $cache;

	public function __construct(
		LoggerInterface $logger,
		CacheInterface $cache,
		?string $interval = null,
		?LoggerInterface $limitLogger = null
	) {
		$this->logger = $logger;
		$this->cache = $cache;
		$this->interval = $interval ?? self::INTERVAL_DAY;
		$this->now = time();
		$this->myLogger = $limitLogger ?? new NullLogger();
	}

	public function log($level, $message, array $context = []): void
	{
		$key = $this->buildKey($message, $context);
		if ($this->cache->canLog($key, $this->getNextExpireTime())) {
			$this->logger->log($level, $message, $context);
		}
		else {
			$this->myLogger->debug('HIT', [
				'key' => $key,
				'now' => $this->now,
			]);
		}
	}

	public function limitCallback(string $key, \Closure $callback): void
	{
		$key = self::CACHE_PREFIX . $key;
		if ($this->cache->canLog($key, $this->getNextExpireTime())) {
			$callback($this->logger);
		}
		else {
			$this->myLogger->debug('HIT - callback', [
				'key' => $key,
				'now' => $this->now,
			]);
		}
	}

	private function buildKey(string $message, array $context): string
	{
		return self::CACHE_PREFIX . \md5(\serialize(['m' => $message, 'c' => $context]));
	}

	private function createDateInterval(): \DateInterval
	{
		return new \DateInterval($this->interval);
	}

	private function getNextExpireTime(): \DateTimeInterface
	{
		$now = \DateTimeImmutable::createFromFormat('U', $this->now);
		$dateInterval = $this->createDateInterval();
		return $now->add($dateInterval);
	}
}
