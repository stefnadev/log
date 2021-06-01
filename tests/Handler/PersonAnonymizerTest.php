<?php declare(strict_types=1);

namespace Stefna\Logger\Handler;

use PHPUnit\Framework\TestCase;
use Stefna\Logger\PII\Anonymizer\PersonAnonymizer;

final class PersonAnonymizerTest extends TestCase
{
	public function testEmail()
	{
		$anonymizer = new PersonAnonymizer();

		$email = 'test@example.com';

		$anonEmail = $anonymizer->process(PersonAnonymizer::EMAIL, $email);

		$this->assertSame('t****@example.com', $anonEmail);
	}

	public function testPhone()
	{
		$anonymizer = new PersonAnonymizer();

		$phone = '123-9876';

		$anonPhone = $anonymizer->process(PersonAnonymizer::PHONE, $phone);

		$this->assertSame('1****76', $anonPhone);
	}

	public function testName()
	{
		$anonymizer = new PersonAnonymizer();

		$name = 'Albert Testsson';

		$anonName = $anonymizer->process(PersonAnonymizer::NAME, $name);

		$this->assertSame('A**** T****', $anonName);
	}

}
