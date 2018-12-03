<?php declare(strict_types=1);

namespace Stefna\Logger\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class BugsnagHandler extends AbstractProcessingHandler
{
	public const IGNORE = 'bugsnagHandlerIgnoreField';

	/**
	 * monolog error codes mapped on to bugSnag severities.
	 *
	 * @var string[]
	 */
	private const SEVERITY_MAPPING = [
		Logger::DEBUG     => 'info',
		Logger::INFO      => 'info',
		Logger::NOTICE    => 'info',
		Logger::WARNING   => 'warning',
		Logger::ERROR     => 'error',
		Logger::CRITICAL  => 'error',
		Logger::ALERT     => 'error',
		Logger::EMERGENCY => 'error'
	];

	protected $client;

	public function __construct(\Bugsnag\Client $client, $level = Logger::ERROR, $bubble = true)
	{
		parent::__construct($level, $bubble);
		$this->client = $client;
	}


	/**
	 * @inheritdoc
	 */
	protected function write(array $record): void
	{
		if (isset($record['context'][self::IGNORE])) {
			return;
		}

		$severity = $this->getSeverity($record['level']);
		if (isset($record['context']['exception'])) {
			$this->client->notifyException(
				$record['context']['exception'],
				function (\Bugsnag\Report $report) use ($record, $severity) {
					$report->setSeverity($severity);
					if (isset($record['extra'])) {
						$report->setMetaData($record['extra']);
					}
				}
			);
		}
		else {
			$this->client->notifyError(
				(string) $record['message'],
				(string) $record['formatted'],
				function (\Bugsnag\Report $report) use ($record, $severity) {
					$report->setSeverity($severity);
					if (isset($record['extra'])) {
						$report->setMetaData($record['extra']);
					}
				}
			);
		}
	}

	/**
	 * Returns the Bugsnag severity from a monolog error code.
	 *
	 * @param int $errorCode - one of the Logger:: constants.
	 * @return string
	 */
	private function getSeverity($errorCode): string
	{
		return self::SEVERITY_MAPPING[$errorCode] ?? self::SEVERITY_MAPPING[Logger::ERROR];
	}
}
