<?php declare(strict_types=1);

namespace Stefna\Logger\Exceptions;

class LogLevelNotFoundException extends \InvalidArgumentException
{
	public function __construct(string $level)
	{
		parent::__construct("Log-level not found: {$level}");
	}
}
