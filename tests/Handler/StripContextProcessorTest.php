<?php declare(strict_types=1);

namespace Stefna\Logger\Handler;

use PHPUnit\Framework\TestCase;
use Stefna\Logger\Mock\StubContextLogRecord;
use Stefna\Logger\Processor\StripContextProcessor;

final class StripContextProcessorTest extends TestCase
{
	public function test()
	{
		$stripProcessor = new StripContextProcessor();
		$stripProcessor->addField('beginningWith%');
		$stripProcessor->addField('%endingWith');
		$stripProcessor->addField('%contain%');

		$context = [
			'beginningWith_Underscore' => 'test',
			'beginningWithCamelCase' => 'test',
			'BeginningwithCaseInsensitive' => 'test',
			'beginningWith' => 'test',
			'not_beginningWith' => 'stay',
			'notBeginningWith' => 'stay',

			'underscore_endingWith' => 'test',
			'camelCaseEndingWith' => 'test',
			'endingWith' => 'test',
			'EndingWith_not' => 'stay',
			'EndingWithNot' => 'stay',

			'contain' => 'test',
			'pre_contain' => 'test',
			'contain_post' => 'test',
			'pre_contain_post' => 'test',
			'precontainpost' => 'test',
			'preCoNtAiNpost' => 'test',
		];

		$expectedContext = [
			'not_beginningWith' => 'stay',
			'notBeginningWith' => 'stay',
			'EndingWith_not' => 'stay',
			'EndingWithNot' => 'stay',
		];

		$processedContext = $stripProcessor(new StubContextLogRecord($context))->context;

		$this->assertSame($expectedContext, $processedContext);
	}

	public function testDefaultValue()
	{
		$stripProcessor = new StripContextProcessor();

		$context = [
			'password' => 'test',
			'passwordPost' => 'test',
			'prePassword' => 'test',
			'password_post' => 'test',
			'pre_password' => 'test',
			'pre_password_post' => 'test',
			'prePasswordPost' => 'test',
		];

		$expectedContext = [];

		$processedContext = $stripProcessor(new StubContextLogRecord($context))->context;

		$this->assertSame($expectedContext, $processedContext);
	}
}
