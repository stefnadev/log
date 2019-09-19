<?php

namespace Stefna\Logger\Handler;

use Bugsnag\Client;
use Bugsnag\Report;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class BugsnagHandlerTest extends TestCase
{
	/** @var Client|\PHPUnit\Framework\MockObject\MockObject */
	private $client;

	protected function setUp(): void
	{
		$this->client = $this->createMock(Client::class);
	}

	public function testDefaultBehaviour(): void
	{
		$report = $this->createMock(Report::class);
		$report->expects($this->once())->method('setSeverity')->with('error');
		$report->expects($this->once())->method('setMetaData')->with([]);

		$this->client->expects($this->once())->method('notifyError')->with(
			'test',
			$this->stringContains('test'),
			$this->callback(function ($callable) use($report) {
				$callable($report);

				return true;
			})
		);
		$handler = new BugsnagHandler($this->client);
		$logger = new Logger('test', [$handler]);
		$logger->error('test', [
			'not_included' => true,
		]);
	}

	public function testIncludeContext(): void
	{
		$context = [
			'included' => true,
		];

		$report = $this->createMock(Report::class);
		$report->expects($this->once())->method('setSeverity')->with('error');
		$report->expects($this->exactly(2))->method('setMetaData')->withConsecutive([[], true], [$context, true]);

		$this->client->expects($this->once())->method('notifyError')->with(
			'test',
			$this->stringContains('test'),
			$this->callback(function ($callable) use($report) {
				$callable($report);

				return true;
			})
		);
		$handler = new BugsnagHandler($this->client, Logger::ERROR, true, true);
		$logger = new Logger('test', [$handler]);
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
}
