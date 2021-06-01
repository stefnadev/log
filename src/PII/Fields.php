<?php declare(strict_types=1);

namespace Stefna\Logger\PII;

use Stefna\Logger\PII\Anonymizer\CardAnonymizer;
use Stefna\Logger\PII\Anonymizer\PersonAnonymizer;

interface Fields
{
	public const CARD_NUMBER = CardAnonymizer::CARD_NUMBER;
	public const CARD_CCV = CardAnonymizer::CARD_CCV;
	public const CARD_HOLDER = CardAnonymizer::CARD_HOLDER;

	public const NAME = PersonAnonymizer::NAME;
	public const PHONE = PersonAnonymizer::PHONE;
	public const EMAIL = PersonAnonymizer::EMAIL;
	public const SSN = PersonAnonymizer::SSN;
	public const DOB = PersonAnonymizer::DOB;
}
