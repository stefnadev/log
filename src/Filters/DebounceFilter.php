<?php declare(strict_types=1);

namespace Stefna\Logger\Filters;

class DebounceFilter implements FilterInterface
{
	public const DEBOUNCE_INTERVAL = '_debounce_interval';

	/** @var callable */
	private $debounceCallback;

	public function __construct(callable $debounceCallback)
	{
		$this->debounceCallback = $debounceCallback;
	}

	/**
	 * @inheritdoc
	 */
	public function __invoke(string $level, string|\Stringable $message, array $context = []): bool
	{
		// no interval in context skip running debouncer
		if (!isset($context[self::DEBOUNCE_INTERVAL])) {
			return true;
		}

		if (!$context[self::DEBOUNCE_INTERVAL] instanceof \DateInterval) {
			$context[self::DEBOUNCE_INTERVAL] = new \DateInterval($context[self::DEBOUNCE_INTERVAL]);
		}

		$callback = $this->debounceCallback;
		return (bool)$callback($level, $message, $context);
	}
}
