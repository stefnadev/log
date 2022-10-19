<?php

namespace Stefna\Logger\Handler;

use Bugsnag\Client;
use Bugsnag\Configuration;
use Bugsnag\Report;
use Monolog\Level;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class BugsnagHandlerTest extends TestCase
{
	/** @var Client&\PHPUnit\Framework\MockObject\MockObject */
	private $client;

	protected function setUp(): void
	{
		$this->client = $this->createMock(Client::class);
	}

	public function testDefaultBehaviour(): void
	{
		$loggerChannel = 'testChannel';
		$report = $this->createMock(Report::class);
		$report->expects($this->once())->method('setSeverity')->with('error');
		$report
			->expects($this->exactly(2))
			->method('setMetaData')
			->withConsecutive(
				[['channel' => $loggerChannel], true],
				[[], true]
			);

		$this->client->expects($this->once())->method('notifyError')->with(
			'test',
			$this->stringContains('test'),
			$this->callback(function ($callable) use($report) {
				$callable($report);

				return true;
			})
		);
		$handler = new BugsnagHandler($this->client);
		$logger = new Logger($loggerChannel, [$handler]);
		$logger->error('test', [
			'not_included' => true,
		]);
	}

	public function testEmptyFilterNamespaces(): void
	{
		$report = Report::fromPHPThrowable(new Configuration(''), new \Exception('test'));

		$handler = new BugsnagHandler($this->client);
		$handler->setFilter([]);

		$frameCount = count($report->getStacktrace()->getFrames());

		$handler->cleanStacktrace($report);

		$this->assertCount($frameCount, $report->getStacktrace()->getFrames());
	}

	public function testNoneExistingNamespaceFilter(): void
	{
		$report = Report::fromPHPThrowable(new Configuration(''), new \Exception('test'));

		$handler = new BugsnagHandler($this->client);
		$handler->setFilter(['Sunkan\\']);

		$frameCount = count($report->getStacktrace()->getFrames());

		$handler->cleanStacktrace($report);

		$this->assertCount($frameCount, $report->getStacktrace()->getFrames());
	}

	public function testFilterNamespaces(): void
	{
		$report = Report::fromPHPThrowable(new Configuration(''), new \Exception('test'));

		$handler = new BugsnagHandler($this->client);
		$handler->setFilter(['PHPUnit\\']);

		$frameCount = count($report->getStacktrace()->getFrames());

		$handler->cleanStacktrace($report);

		$this->assertCount($frameCount - 10, $report->getStacktrace()->getFrames());
	}

	public function testIncludeContext(): void
	{
		$loggerChannel = 'test-channel';
		$context = [
			'included' => true,
		];

		$report = $this->createMock(Report::class);
		$report->expects($this->once())->method('setSeverity')->with('error');
		$report
			->expects($this->exactly(3))
			->method('setMetaData')
			->withConsecutive(
				[['channel' => $loggerChannel], true],
				[[], true],
				[$context, true]
			);

		$this->client->expects($this->once())->method('notifyError')->with(
			'test',
			$this->stringContains('test'),
			$this->callback(function ($callable) use($report) {
				$callable($report);

				return true;
			})
		);
		$handler = new BugsnagHandler($this->client, Level::Error, true, true);
		$logger = new Logger($loggerChannel, [$handler]);
		$logger->error('test', $context);
	}

	public function testIgnoreError(): void
	{
		$this->client->expects($this->never())->method('notifyError');
		$handler = new BugsnagHandler($this->client);
		$logger = new Logger('test', [$handler]);
		$logger->error('test', [
			BugsnagHandler::IGNORE => true,
			'not_included' => true,
		]);
	}

	public function testAddBreadcrumb(): void
	{
		$loggerChannel = 'test-logger';
		$context = [
			'included' => true,
		];

		$report = $this->createMock(Report::class);
		$report->expects($this->once())->method('setSeverity')->with('error');
		$report
			->expects($this->exactly(3))
			->method('setMetaData')
			->withConsecutive(
				[['channel' => $loggerChannel], true],
				[[], true],
				[$context, true]
			);

		$this->client->expects($this->once())->method('leaveBreadcrumb')->with(
			'Log DEBUG',
			'log',
			$this->isType('array')
		);
		$this->client->expects($this->once())->method('notifyError')->with(
			'test',
			$this->stringContains('test'),
			$this->callback(function ($callable) use($report) {
				$callable($report);

				return true;
			})
		);
		$handler = new BugsnagHandler($this->client, Level::Error, true, true, true);
		$logger = new Logger($loggerChannel, [$handler]);
		$logger->debug('breadcrumb');
		$logger->error('test', $context);
	}
}
