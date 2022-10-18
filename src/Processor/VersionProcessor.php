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
		$context = $record->context;
		if ($this->version->release) {
			$context['release'] = $this->version->release;
		}
		if ($this->version->version) {
			$context['version'] = $this->version->version;
		}
		if ($this->version->commit) {
			$context['commit'] = $this->version->commit;
		}

		return $record->with(context: $context);
	}
}
