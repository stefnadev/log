<?php declare(strict_types=1);

namespace Stefna\Logger\Limiter;

interface CacheInterface
{
	public function canLog(string $key, \DateTimeInterface $nextExpireTime): bool;
}
