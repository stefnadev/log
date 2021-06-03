<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

use Psr\SimpleCache\CacheInterface;

class TimeLimitFilter implements FilterInterface
{
	/** @var CacheInterface */
	private $cache;
	/** @var \DateInterval */
	private $interval;

	public function __construct(CacheInterface $cache, \DateInterval $interval)
	{
		$this->cache = $cache;
		$this->interval = $interval;
	}

	/**
	 * @param array<string, mixed> $context
	 */
	public function __invoke(string $level, string $message, array $context): bool
	{
		$key = md5(serialize([$level, $message, $context]));
		if (!$this->cache->has($key)) {
			$this->cache->set($key, '1', $this->interval);
			return true;
		}

		return false;
	}
}
