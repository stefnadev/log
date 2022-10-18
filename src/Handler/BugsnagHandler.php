<?php declare(strict_types=1);

namespace Stefna\Logger\Handler;

use Bugsnag\Client as BugsnagClient;
use Bugsnag\Report;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

class BugsnagHandler extends AbstractProcessingHandler
{
	public const IGNORE = 'bugsnagHandlerIgnoreField';

	/** @var array<array-key, string> */
	private array $filter;

	public function __construct(
		protected BugsnagClient $client,
		private readonly Level $realLevel = Level::Error,
		bool $bubble = true,
		private readonly bool $includeContext = false,
		private readonly bool $addBreadCrumbs = false
	) {
		parent::__construct($addBreadCrumbs ? Level::Debug : $this->realLevel, $bubble);
		$this->client->registerCallback([$this, 'cleanStacktrace']);
	}

	/**
	 * @param array<array-key, string> $filter Namespaces to ignore
	 */
	public function setFilter(array $filter): void
	{
		$this->filter = $filter;
	}

	protected function write(LogRecord $record): void
	{
		if (isset($record->context[self::IGNORE])) {
			return;
		}

		if ($this->addBreadCrumbs && $record->level->isLowerThan($this->realLevel)) {
			$title = 'Log ' . $record->level->getName();
			$context = $record->context;

			if (isset($context['exception'])) {
				$title = get_class($context['exception']);
				$data = ['name' => $title, 'message' => $context['exception']->getMessage()];
				unset($context['exception']);
			}
			else {
				$data = ['message' => $record->message];
			}
			$metaData = array_merge($data, $context);
			$this->client->leaveBreadcrumb($title, 'log', array_filter($metaData));

			return ;
		}

		$severity = match($record->level) {
			Level::Error, Level::Critical, Level::Alert, Level::Emergency => 'error',
			Level::Debug, Level::Info, Level::Notice => 'info',
			Level::Warning   => 'warning',
		};
		$reportCallback = function (Report $report) use ($record, $severity) {
			$report->setSeverity($severity);
			if (isset($record->channel)) {
				$report->setMetaData(['channel' => $record->channel]);
			}
			if (isset($record->extra)) {
				$report->setMetaData($record->extra);
			}
			if ($this->includeContext) {
				$context = $record->context;
				unset($context['exception']);
				$report->setMetaData($record->context);
			}
		};

		if (isset($record->context['exception'])) {
			$this->client->notifyException(
				$record->context['exception'],
				$reportCallback
			);
		}
		else {
			$this->client->notifyError(
				$record->message,
				(string) $record->formatted,
				$reportCallback
			);
		}
	}

	public function cleanStacktrace(Report $report): void
	{
		$stacktrace = $report->getStacktrace();

		if ($this->filter) {
			$frames = $stacktrace->getFrames();
			foreach ($this->filter as $namespace) {
				if (str_starts_with($frames[0]['method'], $namespace)) {
					//if an error have happened in one of the filtered namespaces don't remove that information
					break;
				}
				// This is a workaround for not being allowed to replace stacktrace in report
				/** @noinspection CallableInLoopTerminationConditionInspection */
				for ($i = 0; $i < count($frames); $i++) {
					if (str_starts_with($frames[$i]['method'], $namespace)) {
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
		while (str_starts_with($stacktrace->getFrames()[0]['method'], 'Monolog\\') ||
			str_starts_with($stacktrace->getFrames()[0]['method'], 'Stefna\\Logger\\')
		) {
			$stacktrace->removeFrame(0);
		}
	}
}
