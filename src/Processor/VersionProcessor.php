<?php declare(strict_types=1);

namespace Stefna\Logger\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class VersionProcessor implements ProcessorInterface
{
	public function __construct(
		private readonly VersionObject $version,
	) {}

	public function __invoke(LogRecord $record): LogRecord
	{
		if ($this->version->release) {
			$record->extra['release'] = $this->version->release;
		}
		if ($this->version->version) {
			$record->extra['version'] = $this->version->version;
		}
		if ($this->version->commit) {
			$record->extra['commit'] = $this->version->commit;
		}

		return $record;
	}
}
