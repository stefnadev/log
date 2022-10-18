<?php declare(strict_types=1);

namespace Stefna\Logger\Processor;

class VersionObject
{
	/**
	 * @param array{release: string, commit: string, version: string} $versionData
	 */
	public static function fromReleaseTool(array $versionData): self
	{
		return new self($versionData['release'], $versionData['commit'], $versionData['version']);
	}

	public function __construct(
		public readonly string $release,
		public readonly string $commit = '',
		public readonly string $version = '',
		public readonly string $environment = '',
	) {}
}
