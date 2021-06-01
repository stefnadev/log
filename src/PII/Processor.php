<?php declare(strict_types=1);

namespace Stefna\Logger\PII;

use Stefna\Logger\PII\Anonymizer\Anonymizer;
use Stefna\Logger\PII\Anonymizer\CardAnonymizer;
use Stefna\Logger\PII\Anonymizer\PasswordAnonymizer;
use Stefna\Logger\PII\Anonymizer\PersonAnonymizer;

final class Processor
{
	/** @var Anonymizer[] */
	private $anonymizers;

	public function __construct(Anonymizer ...$anonymizers)
	{
		$this->anonymizers = $anonymizers;
		if (!$this->anonymizers) {
			$this->anonymizers[] = new PasswordAnonymizer();
			$this->anonymizers[] = new CardAnonymizer();
			$this->anonymizers[] = new PersonAnonymizer();
		}
	}

	public function __invoke(array $record)
	{
		foreach ($record['context'] as $key => $value) {
			foreach ($this->anonymizers as $anonymizer) {
				if (!$anonymizer->support($key)) {
					continue;
				}
				$value = $anonymizer->process($key, $value);
				if ($value === null) {
					unset($record['context'][$key]);
					continue 2;
				}
				$record['context'][$key] = $value;
			}
		}

		return $record;
	}
}
