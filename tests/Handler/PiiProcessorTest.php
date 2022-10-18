<?php declare(strict_types=1);

namespace Stefna\Logger\Handler;

use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;
use Stefna\Logger\Mock\StubContextLogRecord;
use Stefna\Logger\PII\Fields;
use Stefna\Logger\PII\Processor;

final class PiiProcessorTest extends TestCase
{
	public function testCardData()
	{
		$context = [
			Fields::CARD_HOLDER => 'Test Testsson',
			Fields::CARD_NUMBER => '1234-4321-5678-9876',
			'transaction' => 123,
			'type' => 'borgun'
		];
		$expectedContext = [
			Fields::CARD_HOLDER => 'T**** T****',
			Fields::CARD_NUMBER => '12**-****-****-9876',
			'transaction' => 123,
			'type' => 'borgun'
		];

		$processor = new Processor();
		$record = $processor(new StubContextLogRecord($context));

		$this->assertSame($expectedContext, $record['context']);
	}

	public function testPerson()
	{
		$context = [
			Fields::NAME => 'Test Testsson',
			Fields::SSN => '1234567890',
			Fields::EMAIL => 'test@example.com',
			Fields::PHONE => '123-3214',
			Fields::DOB => '1985-06-14',
			'account' => 123,
			'type' => 'guest',
		];
		$expectedContext = [
			Fields::NAME => 'T**** T****',
			Fields::SSN => '**********',
			Fields::EMAIL => 't****@example.com',
			Fields::PHONE => '1****14',
			'account' => 123,
			'type' => 'guest',
		];

		$processor = new Processor();
		$record = $processor(new StubContextLogRecord($context));

		$this->assertSame($expectedContext, $record['context']);
	}

	public function testMixed()
	{
		$context = [
			Fields::CARD_HOLDER => 'Test Testsson',
			Fields::CARD_NUMBER => '12343214569874563',
			Fields::SSN => '1234567890',
			Fields::EMAIL => 'test@example.com',
			Fields::PHONE => '123-3214',
			'account' => 123,
			'type' => 'guest',
		];
		$expectedContext = [
			Fields::CARD_HOLDER => 'T**** T****',
			Fields::CARD_NUMBER => '12**-****-****-4563',
			Fields::SSN => '**********',
			Fields::EMAIL => 't****@example.com',
			Fields::PHONE => '1****14',
			'account' => 123,
			'type' => 'guest',
		];

		$processor = new Processor();
		$record = $processor(new StubContextLogRecord($context));

		$this->assertSame($expectedContext, $record['context']);
	}

	public function testNested()
	{
		$context = [
			Fields::CARD_HOLDER => 'Test Testsson',
			'account' => [
				Fields::SSN => '1234567890',
				Fields::NAME => 'Test Testsson',
				Fields::EMAIL => 'test@example.com',
			],
			'type' => 'guest',
		];
		$expectedContext = [
			Fields::CARD_HOLDER => 'T**** T****',
			'account' => [
				Fields::SSN => '**********',
				Fields::NAME => 'T**** T****',
				Fields::EMAIL => 't****@example.com',
			],
			'type' => 'guest',
		];

		$processor = new Processor();
		$record = $processor(new StubContextLogRecord($context));

		$this->assertSame($expectedContext, $record['context']);
	}

	public function testWithArrayList()
	{
		$context = [
			Fields::CARD_HOLDER => 'Test Testsson',
			'accounts' => [
				[
					Fields::SSN => '1234567890',
					Fields::NAME => 'Test Testsson',
					Fields::EMAIL => 'test@example.com',
				]
			],
			'list' => [
				'1',
				'2',
				'3',
			],
			'type' => 'guest',
		];
		$expectedContext = [
			Fields::CARD_HOLDER => 'T**** T****',
			'accounts' => [
				[
					Fields::SSN => '**********',
					Fields::NAME => 'T**** T****',
					Fields::EMAIL => 't****@example.com',
				],
			],
			'list' => [
				'1',
				'2',
				'3',
			],
			'type' => 'guest',
		];

		$processor = new Processor();
		$record = $processor(new StubContextLogRecord($context));

		$this->assertSame($expectedContext, $record['context']);
	}
}
