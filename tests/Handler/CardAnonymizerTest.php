<?php declare(strict_types=1);

namespace Stefna\Logger\Handler;

use PHPUnit\Framework\TestCase;
use Stefna\Logger\PII\Anonymizer\CardAnonymizer;

final class CardAnonymizerTest extends TestCase
{
	public function testCardNumber()
	{
		$anonymizer = new CardAnonymizer();

		$cardNumber = '1111-1111-1111-1234';

		$this->assertSame(
			'11**-****-****-1234',
			$anonymizer->process(CardAnonymizer::CARD_NUMBER, $cardNumber)
		);
	}

	public function testCardCcv()
	{
		$anonymizer = new CardAnonymizer();

		$cardCcv = '1234';

		$this->assertNull($anonymizer->process(CardAnonymizer::CARD_CCV, $cardCcv));
	}

	public function testCardHolder()
	{
		$anonymizer = new CardAnonymizer();

		$cardHolder = 'Test Sub Last';

		$this->assertSame(
			'T**** S**** L****',
			$anonymizer->process(CardAnonymizer::CARD_HOLDER, $cardHolder)
		);
	}
}
