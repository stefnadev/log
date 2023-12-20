<?php declare(strict_types=1);

namespace Stefna\Logger\Processor;

use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stefna\Logger\Logger\ProcessLogger;

final class CallableContextTest extends TestCase
{
	public function testDeferredValue(): void
	{
		$mainLogger = $this->createMock(LoggerInterface::class);

		$logger = new ProcessLogger($mainLogger, new CallableContextProcessor());

		$msg = 'test';
		$mainLogger->expects($this->once())
			->method('log')
			->with(
				LogLevel::NOTICE,
				$msg,
				$this->callback(function($context) {
					$this->assertSame('complexValue', $context['deferredValue']);
					return true;
				})
			);

		$logger->notice($msg, [
			'deferredValue' => function () {
				return 'complexValue';
			}
		]);
	}

	public function testCallbackValueIsRemoved(): void
	{
		$mainLogger = $this->createMock(LoggerInterface::class);
		$logger = new ProcessLogger($mainLogger, new CallableContextProcessor());
		$msg = 'testCallbackValueIsRemoved';
		$callbackExecuted = false;

		$mainLogger->expects($this->once())
			->method('log')
			->with(
				LogLevel::NOTICE,
				$msg,
				$this->callback(function($context) {
					$this->assertArrayNotHasKey(CallableContextProcessor::CALLBACK, $context);
					return true;
				})
			);

		$logger->notice($msg, [
			CallableContextProcessor::CALLBACK => function () use (&$callbackExecuted) {
				$callbackExecuted = true;
			},
		]);

		$this->assertTrue($callbackExecuted);
	}

	public function testStringCallbacksBeingSkipped(): void
	{
		$processor = new CallableContextProcessor();

		$logRecord = new LogRecord(
			new \DateTimeImmutable(),
			'test',
			Level::Alert,
			'message',
			[
				'nativeFunction' => 'header',
				'namespacedFunction' => 'Stefna\Logger\Processor\testingFunction',
				'deferredValue' => function () {
					return 'complexValue';
				}
			],
		);

		$modifiedContext = $processor($logRecord);
		$this->assertSame([
			'nativeFunction' => 'header',
			'namespacedFunction' => 'Stefna\Logger\Processor\testingFunction',
			'deferredValue' => 'complexValue',
		], $modifiedContext->context);
	}

	public function testStringFunctionIsNotCalled():void
	{
		$stringFunction  = 'Stefna\Logger\Processor\testingFunction';

		$mainLogger = $this->createMock(LoggerInterface::class);
		$logger = new ProcessLogger($mainLogger, new CallableContextProcessor());
		$msg = 'testCallbackValueIsRemoved';
		$callbackExecuted = false;

		$this->expectOutputString("");
		$logger->debug($msg, [
			"section" => $stringFunction,
			CallableContextProcessor::CALLBACK => function () use (&$callbackExecuted) {
				$callbackExecuted = true;
			},
		]);

		$this->assertTrue($callbackExecuted);
	}
}

function testingFunction(): never
{
	CallableContextTest::fail("Shouldn't be called");
}
