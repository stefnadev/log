<?php declare(strict_types=1);

namespace Stefna\Logger\Processor;

class VersionObject
{
	/** @var string */
	private $version;
	/** @var string */
	private $release;
	/** @var string */
	private $commit;
	/** @var string */
	private $environment;

	/**
	 * @param array{release: string, commit: string, version: string} $versionData
	 */
	public static function fromReleaseTool(array $versionData): self
	{
		return new self($versionData['release'], $versionData['commit'], $versionData['version']);
	}

	public function __construct(string $release, string $commit = '', string $version = '', string $environment = '')
	{
		$this->version = $version;
		$this->release = $release;
		$this->commit = $commit;
		$this->environment = $environment;
	}

	public function getVersion(): string
	{
		return $this->version;
	}

	public function getRelease(): string
	{
		return $this->release;
	}

	public function getCommit(): string
	{
		return $this->commit;
	}

	public function getEnvironment(): string
	{
		return $this->environment;
	}
}
