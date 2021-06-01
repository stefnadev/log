<?php declare(strict_types=1);

namespace Stefna\Logger\PII\Anonymizer;

interface Anonymizer
{
	public function support(string $key): bool;

	public function process(string $key, $value);
}
