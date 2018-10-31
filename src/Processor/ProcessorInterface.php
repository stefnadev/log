<?php declare(strict_types=1);

namespace Stefna\Logger\Processor;

use Stefna\Logger\Wrapper\LogRecord;

interface ProcessorInterface
{
	public function __invoke(LogRecord $record);
}
