<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

interface FilterInterface
{
	/**
	 * @param array<string, mixed> $context
	 */
	public function __invoke(string $level, string $message, array $context): bool;
}
