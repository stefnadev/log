<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

class CallbackFilter implements FilterInterface
{
	public const KEY = 'callback';

	/** @var callable */
	private $callback;

	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}

	/**
	 * @inheritdoc
	 */
	public function __invoke(string $level, string $message, array $context = []): bool
	{
		$callback = $this->callback;
		return (bool)$callback($level, $message, $context);
	}
}
