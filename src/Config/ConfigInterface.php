<?php declare(strict_types=1);

namespace Stefna\Logger\Config;

use Stefna\Logger\Filters\FilterInterface;

interface ConfigInterface
{
	public function getProcessors(): array;

	public function getHandlers(): array;

	public function getName(): string;

	public function getFilters(): array;
}
