<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

class CallbackFilter implements FilterInterface
{
	/** @var callable */
	private $callback;

	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}

	/**
	 * @inheritdoc
	 */
	public function __invoke($level, $message, array $context = []): bool
	{
		$callback = $this->callback;
		return (bool)$callback($level, $message, $context);
	}
}
