<?php declare(strict_types=1);

namespace Stefna\Logger\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

final class StripContextProcessor implements ProcessorInterface
{
	private const DEFAULT_FIELDS = [
		'%password%',
	];

	/** @var string[] */
	private array $fields = [];
	/** @var array<array-key, array{field: string, type: string}> */
	private array $wildCardFields = [];

	public function __construct(string ...$fields)
	{
		$fields = $fields + self::DEFAULT_FIELDS;
		foreach ($fields as $field) {
			$this->addField($field);
		}
	}

	public function addField(string $field): void
	{
		$wildcardCount = substr_count($field, '%');
		if ($wildcardCount) {
			$wildcardField = [
				'field' => strtolower(str_replace('%', '', $field)),
				'type' => $wildcardCount === 2 ? 'containing' : 'beginning',
			];
			if ($wildcardCount === 1 && str_starts_with($field, '%')) {
				$wildcardField['type'] = 'ending';
			}

			$this->wildCardFields[] = $wildcardField;
		}
		else {
			$this->fields[] = $field;
		}
	}

	public function __invoke(LogRecord $record): LogRecord
	{
		return $record->with(context: $this->processContext($record->context));
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

			if (in_array($key, $this->fields, true)) {
				unset($context[$key]);
				continue;
			}

			$searchKey = strtolower($key);
			foreach ($this->wildCardFields as $field) {
				$pos = strpos($searchKey, $field['field']);
				if ($field['type'] === 'beginning' && $pos === 0) {
					unset($context[$key]);
					continue 2;
				}
				if ($field['type'] === 'containing' && $pos !== false) {
					unset($context[$key]);
					continue 2;
				}
				if ($pos !== false &&
					$field['type'] === 'ending' &&
					str_ends_with($searchKey, $field['field'])
				) {
					unset($context[$key]);
					continue 2;
				}
			}
		}
		return $context;
	}
}
