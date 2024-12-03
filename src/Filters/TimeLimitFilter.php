<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

use Psr\SimpleCache\CacheInterface;

class TimeLimitFilter implements FilterInterface
{
	public const KEY = 'time-limit';

	public function __construct(
		private readonly CacheInterface $cache,
		private readonly \DateInterval $interval,
	) {}

	/**
	 * @param array<string, mixed> $context
	 */
	public function __invoke(string $level, string|\Stringable $message, array $context): bool
	{
		$key = md5(serialize([$level, $message, $context]));
		if (!$this->cache->has($key)) {
			$this->cache->set($key, '1', $this->interval);
			return true;
		}

		return false;
	}
}
