<?php declare(strict_types=1);

namespace Stefna\Logger\Config;

use Monolog\Handler\HandlerInterface;

class Config implements ConfigInterface
{
	/** @var string */
	private $name;
	/** @var array<array-key, array{0: string, 1: array<string, mixed>}> */
	private $filters = [];
	/** @var array<array-key, callable> */
	private $processors = [];
	/** @var array<array-key, HandlerInterface> */
	private $handlers = [];

	/**
	 * @param array<array-key, array{0: string, 1: array<string, mixed>}> $filters
	 * @param array<array-key, callable> $processors
	 * @param array<array-key, HandlerInterface> $handlers
	 */
	public function __construct(string $name, array $filters, array $processors = [], array $handlers = [])
	{
		$this->name = $name;
		$this->filters = $filters;
		$this->processors = $processors;
		$this->handlers = $handlers;
	}

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
