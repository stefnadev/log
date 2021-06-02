<?php declare(strict_types=1);

namespace Stefna\Logger;

class NewRelic
{
	/** @var bool|null */
	private static $enabled;

	public static function enabled(): bool
	{
		if (self::$enabled === null) {
			self::$enabled = \extension_loaded('newrelic');
		}
		return self::$enabled;
	}

	public static function setEnabled(bool $enabled): void
	{
		// Null so we detect if the extension is loaded
		self::$enabled = $enabled ? null : false;
	}

	public static function nameTransaction(string $name): void
	{
		if (self::enabled()) {
			\newrelic_name_transaction($name);
		}
	}

	/**
	 * @param object|class-string $object
	 * @param string[] $remove
	 * @return string
	 */
	public static function cleanClassName($object, array $remove = [])
	{
		$className = \is_object($object) ? \get_class($object) : (string)$object;
		$className = \ltrim(\str_replace('\\', '_', $className), '\\');

		return str_replace($remove, '', $className);
	}

	public static function appName(string $name): void
	{
		if (self::enabled()) {
			\newrelic_set_appname($name);
		}
	}

	public static function backgroundJob(bool $flag = true): void
	{
		if (self::enabled()) {
			\newrelic_background_job($flag);
		}
	}
}
