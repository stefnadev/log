<?php declare(strict_types=1);

namespace Stefna\Logger\Config;

use Monolog\Handler\HandlerInterface;
use Stefna\Logger\Filters\FilterInterface;

class Config implements ConfigInterface
{
	/**
	 * @param array<array-key, FilterInterface|array{0: string, 1: array<string, mixed>}> $filters
	 * @param array<array-key, callable> $processors
	 * @param array<array-key, HandlerInterface> $handlers
	 */
	public function __construct(
		private readonly string $name,
		private readonly array $filters,
		private readonly array $processors = [],
		private readonly array $handlers = [],
	) {}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @inheritdoc
	 */
	public function getFilters(): array
	{
		return $this->filters;
	}

	/**
	 * @inheritdoc
	 */
	public function getProcessors(): array
	{
		return $this->processors;
	}

	/**
	 * @inheritdoc
	 */
	public function getHandlers(): array
	{
		return $this->handlers;
	}
}
