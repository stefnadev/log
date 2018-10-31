<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

interface FilterInterface
{
	public function __invoke(string $level, string $message, array $context): bool;
}
