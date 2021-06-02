<?php declare(strict_types=1);

namespace Stefna\Logger\Processor;

class VersionProcessor
{
	/** @var VersionObject */
	private $version;

	public function __construct(VersionObject $version)
	{
		$this->version = $version;
	}

	/**
	 * @param array{context:array<string, mixed>} $record
	 * @return array{context:array<string, mixed>}
	 */
	public function __invoke($record): array
	{
		if ($this->version->getRelease()) {
			$record['context']['release'] = $this->version->getRelease();
		}
		if ($this->version->getVersion()) {
			$record['context']['version'] = $this->version->getVersion();
		}
		if ($this->version->getCommit()) {
			$record['context']['commit'] = $this->version->getCommit();
		}

		return $record;
	}
}
