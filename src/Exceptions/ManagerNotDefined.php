<?php declare(strict_types=1);

namespace Stefna\Logger\Exceptions;

class ManagerNotDefined extends \RuntimeException
{
	public function __construct()
	{
		parent::__construct('Manager not defined. Please set a default manager before start using.');
	}
}
