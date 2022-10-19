<?php declare(strict_types=1);

namespace Stefna\Logger\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

final class DateTimeProcessor implements ProcessorInterface
{
	private const FORMAT = 'c';

	public function __invoke(LogRecord $record): LogRecord
	{
		$context = $this->processContext($record->context);
		return $record->with(context: $context);
	}

	/**
	 * @param array<string, mixed> $context
	 * @return array<string, mixed>
	 */
	private function processContext(array $context): array
	{
		foreach ($context as $key => $value) {
			if (is_array($value)) {
				$context[$key] = $this->processContext($value);
				continue;
			}

			if ($value instanceof \DateTimeImmutable) {
				$context[$key] = $value->format(self::FORMAT);
			}
		}
		return $context;
	}
}
