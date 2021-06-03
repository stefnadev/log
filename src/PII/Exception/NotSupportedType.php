<?php declare(strict_types=1);

namespace Stefna\Logger\PII\Exception;

final class NotSupportedType extends \RuntimeException
{
	public static function key(string $key): self
	{
		return new self(sprintf('"%s" is not a valid key for anonymizer', $key));
	}
}
