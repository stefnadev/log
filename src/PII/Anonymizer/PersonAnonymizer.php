<?php declare(strict_types=1);

namespace Stefna\Logger\PII\Anonymizer;

use Stefna\Logger\PII\Exception\NotSupportedType;

final class PersonAnonymizer implements Anonymizer
{
	public const NAME = '_name';
	public const PHONE = '_phone';
	public const EMAIL = '_email';
	public const SSN = '_ssn';
	public const DOB = '_date_of_birth';

	/** @var array<string, string> */
	private $aliasFields = [];

	public function addAliasField(string $field, string $alias): void
	{
		$this->aliasFields[$alias] = $field;
	}

	public function support(string $key): bool
	{
		return in_array($key, [
			self::NAME,
			self::PHONE,
			self::EMAIL,
			self::SSN,
			self::DOB,
		], true) || array_key_exists($key, $this->aliasFields);
	}

	/**
	 * @param mixed $value
	 */
	public function process(string $key, $value): ?string
	{
		if (array_key_exists($key, $this->aliasFields)) {
			$key = $this->aliasFields[$key];
		}

		if (in_array($key, [self::DOB], true)) {
			// remove value
			return null;
		}

		if ($key === self::SSN) {
			return '**********';
		}

		if (!is_scalar($value)) {
			return $value;
		}

		$value = (string)$value;
		if ($key === self::EMAIL) {
			$parts = explode('@', $value);
			$domain = array_pop($parts);
			$newValue = '';
			foreach ($parts as $part) {
				$newValue .= $part[0] . '****';
			}
			$newValue .= '@' . $domain;
			return $newValue;
		}
		if ($key === self::PHONE) {
			if (strlen($value) < 3) {
				return '****';
			}
			return $value[0] . '****' . substr($value, -2);
		}
		if ($key === self::NAME) {
			$parts = explode(' ', $value);
			$newValue = '';
			foreach ($parts as $part) {
				$newValue .= $part[0] . '**** ';
			}
			return trim($newValue);
		}

		throw NotSupportedType::key($key);
	}
}
