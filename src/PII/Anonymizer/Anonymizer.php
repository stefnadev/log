<?php declare(strict_types=1);

namespace Stefna\Logger\PII\Anonymizer;

interface Anonymizer
{
	public function support(string $key): bool;

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	public function process(string $key, $value);
}
