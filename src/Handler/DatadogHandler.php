<?php declare(strict_types=1);

namespace Stefna\Logger\Handler;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;

final class DatadogHandler extends AbstractProcessingHandler
{
	private const DATADOG_LOG_HOST = 'https://http-intake.logs.datadoghq.com';

	public const IGNORE = 'datadogHandlerIgnoreField';
	public const TAGS = 'ddtags';
	public const SERVICE = 'service';
	public const HOSTNAME = 'hostname';
	/** @var array<array-key, LogRecord> */
	private array $breadCrumbs = [];

	public function __construct(
		private readonly string $apiKey,
		private readonly string $apiEndpoint = self::DATADOG_LOG_HOST,
		private readonly Level $realLevel = Level::Error,
		bool $bubble = true,
		private readonly bool $addBreadCrumbs = false,
	) {
		parent::__construct($this->addBreadCrumbs ? Level::Debug : $this->realLevel, $bubble);
	}

	public function isHandling(LogRecord $record): bool
	{
		if (isset($record->context[self::IGNORE])) {
			return false;
		}
		return parent::isHandling($record); // TODO: Change the autogenerated stub
	}

	protected function write(LogRecord $record): void
	{
		if ($this->addBreadCrumbs && $record->level->isLowerThan($this->realLevel)) {
			$this->breadCrumbs[] = $this->processRecord($record)->with(context: []);
			return;
		}

		$messages = $this->breadCrumbs;
		$messages[] = $record;

		$this->sendMessages($messages);
		$this->breadCrumbs = [];
	}

	/**
	 * @param LogRecord[] $messages
	 */
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

	protected function getDefaultFormatter(): JsonFormatter
	{
		return new JsonFormatter();
	}
}
