<?php declare(strict_types=1);

namespace Stefna\Logger\Config;

class Config implements ConfigInterface
{
	/*** @var string */
	private $name;
	/** @var array */
	private $filters = [];
	/** @var array */
	private $processors = [];
	/** @var array */
	private $handlers = [];

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

	public function getFilters(): array
	{
		return $this->filters;
	}

	public function getProcessors(): array
	{
		return $this->processors;
	}

	public function getHandlers(): array
	{
		return $this->handlers;
	}
}
