<?php declare(strict_types=1);

namespace Stefna\Logger\Config;

interface ConfigInterface
{
	public function getProcessors(): array;

	public function getHandlers(): array;

	public function getName(): string;

	public function getFilters(): array;
}
