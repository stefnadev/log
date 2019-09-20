<?php declare(strict_types=1);

namespace Stefna\Logger\Handler;

use Bugsnag\Client as BugsnagClient;
use Bugsnag\Report;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class BugsnagHandler extends AbstractProcessingHandler
{
	public const IGNORE = 'bugsnagHandlerIgnoreField';

	/**
	 * Monolog error codes mapped on to bugSnag severities.
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
		Logger::EMERGENCY => 'error',
	];
	/** @var BugsnagClient */
	protected $client;
	/** @var bool */
	private $includeContext;
	/** @var bool */
	private $addBreadCrumbs;
	/** @var int */
	private $realLevel;
	/** @var array */
	private $filter;

	public function __construct(
		BugsnagClient $client,
		int $level = Logger::ERROR,
		bool $bubble = true,
		bool $includeContext = false,
		bool $addBreadCrumbs = false
	) {
		if ($addBreadCrumbs) {
			$this->realLevel = $level;
			$level = Logger::DEBUG;
		}

		parent::__construct($level, $bubble);
		$this->client = $client;
		$this->client->registerCallback([$this, 'cleanStacktrace']);
		$this->includeContext = $includeContext;
		$this->addBreadCrumbs = $addBreadCrumbs;
	}

	/**
	 * @param array $filter Namespaces to ignore
	 */
	public function setFilter(array $filter): void
	{
		$this->filter = $filter;
	}

	/**
	 * @inheritdoc
	 */
	protected function write(array $record): void
	{
		if (isset($record['context'][self::IGNORE])) {
			return;
		}

		if ($this->addBreadCrumbs && $record['level'] < $this->realLevel) {
			$title = 'Log ' . Logger::getLevelName($record['level']);

			if (isset($record['context']['exception'])) {
				$title = get_class($record['context']['exception']);
				$data = ['name' => $title, 'message' => $record['context']['exception']->getMessage()];
				unset($record['context']['exception']);
			}
			else {
				$data = ['message' => $record['message']];
			}
			$metaData = array_merge($data, $record['context']);
			$this->client->leaveBreadcrumb($title, 'log', array_filter($metaData));

			return ;
		}

		$severity = $this->getSeverity($record['level']);
		$reportCallback = function (Report $report) use ($record, $severity) {
			$report->setSeverity($severity);
			if (isset($record['channel'])) {
				$report->setMetaData(['channel' => $record['channel']]);
			}
			if (isset($record['extra'])) {
				$report->setMetaData($record['extra']);
			}
			if ($this->includeContext && isset($record['context'])) {
				unset($record['context']['exception']);
				$report->setMetaData($record['context']);
			}
		};

		if (isset($record['context']['exception'])) {
			$this->client->notifyException(
				$record['context']['exception'],
				$reportCallback
			);
		}
		else {
			$this->client->notifyError(
				(string) $record['message'],
				(string) $record['formatted'],
				$reportCallback
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

	public function cleanStacktrace(Report $report): void
	{
		$stacktrace = $report->getStacktrace();

		if ($this->filter) {
			$frames = $stacktrace->getFrames();
			foreach ($this->filter as $namespace) {
				if (strpos($frames[0]['method'], $namespace) === 0) {
					//if an error have happened in one of the filtered namespaces don't remove that information
					break;
				}
				// This is a workaround for not being allowed to replace stacktrace in report
				/** @noinspection CallableInLoopTerminationConditionInspection */
				for ($i = 0; $i < count($frames); $i++) {
					if (strpos($frames[$i]['method'], $namespace) === 0) {
						$stacktrace->removeFrame($i);
						$frames = $stacktrace->getFrames();
						$i--;
					}
				}
			}
		}

		// Monolog uses MonoSnag for logs, and bugsnag handler logs directly
		$isAMonologHandledLog = $stacktrace->getFrames()[0]['method'] === static::class . '::write';

		if (!$isAMonologHandledLog) {
			// Do nothing
			return;
		}

		// Remove The first frame
		$stacktrace->removeFrame(0);

		// Remove all the trace about Monolog and Stefna\Logger as it's not interesting
		while (strpos($stacktrace->getFrames()[0]['method'], 'Monolog\\') === 0 ||
			strpos($stacktrace->getFrames()[0]['method'], 'Stefna\\Logger\\') === 0
		) {
			$stacktrace->removeFrame(0);
		}
	}
}
