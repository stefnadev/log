<?php declare(strict_types=1);

namespace Stefna\Logger\Handler;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

final class DatadogHandler extends AbstractHandler
{
	private const DATADOG_LOG_HOST = 'https://http-intake.logs.datadoghq.com';

	public const IGNORE = 'datadogHandlerIgnoreField';
	public const TAGS = 'ddtags';
	public const SERVICE = 'service';
	public const HOSTNAME = 'hostname';

	/** @var bool */
	private $addBreadCrumbs;
	/** @var int */
	private $realLevel;
	/** @var string */
	private $apiKey;
	/** @var array<array-key, array<string, mixed>> */
	private $breadCrumbs = [];
	/** @var string */
	private $apiEndpoint;

	public function __construct(
		string $apiKey,
		string $apiEndpoint = self::DATADOG_LOG_HOST,
		int $level = Logger::ERROR,
		bool $bubble = true,
		bool $addBreadCrumbs = false
	) {
		if ($addBreadCrumbs) {
			$this->realLevel = $level;
			$level = Logger::DEBUG;
		}

		parent::__construct($level, $bubble);
		$this->addBreadCrumbs = $addBreadCrumbs;
		$this->apiKey = $apiKey;
		$this->apiEndpoint = $apiEndpoint;
	}

	/**
	 * {@inheritdoc}
	 */
	public function handle(array $record)
	{
		if (isset($record['context'][self::IGNORE])) {
			return false;
		}

		if ($this->addBreadCrumbs && $record['level'] < $this->realLevel) {
			$processedRecord = $this->processRecord($record);
			// don't include context for breadcrumbs
			unset($processedRecord['context']);
			$this->breadCrumbs[] = $processedRecord;
			return true;
		}

		$messages = $this->breadCrumbs;
		$messages[] = $this->processRecord($record);

		$this->sendMessages($messages);
		$this->breadCrumbs = [];

		return false === $this->bubble;
	}

	/**
	 * Processes a record.
	 *
	 * @param  array $record
	 * @return array
	 */
	private function processRecord(array $record): array
	{
		if ($this->processors) {
			foreach ($this->processors as $processor) {
				$record = call_user_func($processor, $record);
			}
		}

		return $record;
	}

	private function sendMessages(array $messages): void
	{
		$url = $this->apiEndpoint . '/v1/input';
		$headers = [
			'DD-API-KEY: ' . $this->apiKey,
			'Content-Type: application/json',
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getFormatter()->formatBatch($messages));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	}

	protected function getDefaultFormatter()
	{
		return new JsonFormatter();
	}
}
