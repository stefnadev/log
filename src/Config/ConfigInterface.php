<?php declare(strict_types=1);

namespace Stefna\Logger\Config;

use Monolog\Handler\HandlerInterface;
use Stefna\Logger\Filters\FilterInterface;

interface ConfigInterface
{
	/**
	 * @return array<array-key, callable>
	 */
	public function getProcessors(): array;

	/**
	 * @return array<array-key, HandlerInterface>
	 */
	public function getHandlers(): array;

	public function getName(): string;

	/**
	 * @return array<array-key, FilterInterface|array{0: string, 1: array<string, mixed>}>
	 */
	public function getFilters(): array;
}
