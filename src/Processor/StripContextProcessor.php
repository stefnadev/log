<?php declare(strict_types=1);

namespace Stefna\Logger\Processor;

final class StripContextProcessor
{
	private const DEFAULT_FIELDS = [
		'%password%',
	];

	/** @var string[] */
	private $fields = [];
	/** @var array<array-key, array{field: string, type: string}> */
	private $wildCardFields = [];

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
			if ($wildcardCount === 1 && strpos($field, '%') === 0) {
				$wildcardField['type'] = 'ending';
			}

			$this->wildCardFields[] = $wildcardField;
		}
		else {
			$this->fields[] = $field;
		}
	}

	/**
	 * @param array{context: array<string, mixed>} $record
	 * @return array{context: array<string, mixed>}
	 */
	public function __invoke(array $record): array
	{
		$record['context'] = $this->processContext($record['context']);

		return $record;
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
					substr($searchKey, -strlen($field['field'])) === $field['field']
				) {
					unset($context[$key]);
					continue 2;
				}
			}
		}
		return $context;
	}
}
