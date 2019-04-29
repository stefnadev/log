<?php declare(strict_types=1);

namespace Stefna\Logger\Exceptions;

class ConfigurationNotDefined extends \DomainException
{
	public function __construct(string $channel)
	{
		parent::__construct("No configuration found for channel: \"$channel\"");
	}
}
