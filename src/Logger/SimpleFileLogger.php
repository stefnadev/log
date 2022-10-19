<?php declare(strict_types=1);

namespace Stefna\Logger\Logger;

use Psr\Log\AbstractLogger;

/**
 * Super simple file logger not to be used in web apps
 *
 * This is a simple logger that can be paired
 *
 * @package Stefna\Logger\Logger
 */
class SimpleFileLogger extends AbstractLogger
{

	public function __construct(
		private readonly string $filePath,
	) {}

	/**
	 * @inheritdoc
	 * @param array<mixed> $context
	 */
	public function log($level, string|\Stringable $message, array $context = []): void
	{
		$messageFormat = "[%s] %s: %s %s\n";
		$message = sprintf($messageFormat, date('Y-m-d H:i:s:v'), $level, $message, json_encode($context));

		file_put_contents($this->filePath, $message, FILE_APPEND);
	}
}
