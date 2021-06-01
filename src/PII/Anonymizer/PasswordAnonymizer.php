<?php declare(strict_types=1);

namespace Stefna\Logger\PII\Anonymizer;

final class PasswordAnonymizer implements Anonymizer
{
	public const PASSWORD = '_password';

	private const DEFAULT_FIELDS = [
		self::PASSWORD,
		'password',
	];

	/** @var string[] */
	private $fields;

	public function __construct(string ...$fields)
	{
		$this->fields = $fields + self::DEFAULT_FIELDS;
	}

	public function addField(string $field): void
	{
		$this->fields[] = $field;
	}

	public function support(string $key): bool
	{
		return in_array($key, $this->fields, true);
	}

	public function process(string $key, $value)
	{
		return null;
	}
}
