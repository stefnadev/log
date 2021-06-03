<?php declare(strict_types=1);

namespace Stefna\Logger\PII\Anonymizer;

use Stefna\Logger\PII\Exception\NotSupportedType;

final class CardAnonymizer implements Anonymizer
{
	public const CARD_CCV = '_card_ccv';
	public const CARD_HOLDER = '_card_holder';
	public const CARD_NUMBER = '_card_number';

	public function support(string $key): bool
	{
		return in_array($key, [
			self::CARD_HOLDER,
			self::CARD_NUMBER,
			self::CARD_CCV,
		]);
	}

	/**
	 * @param mixed $value
	 */
	public function process(string $key, $value): ?string
	{
		if ($key === self::CARD_CCV) {
			// remove value
			return null;
		}

		if (!is_scalar($value)) {
			return $value;
		}

		$value = (string)$value;
		if ($key === self::CARD_NUMBER) {
			return substr($value, 0, 2) . '**-****-****-' . substr($value, -4);
		}

		if ($key === self::CARD_HOLDER) {
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
