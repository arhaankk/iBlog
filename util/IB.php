<?php
/**
 * Simple setup class to manage everything else.
 * This should be included once in every instance of the application.
 */

class IB
{
	protected static $app = null;
	protected static $utilDir = __DIR__;

	public static function autoload(string $class) : void
	{
		$file = self::getUtilDir() . '/' . str_replace('IB\\', '', $class) . '.php';
		if (file_exists($file)) {
			require_once $file;
		}
	}

	public static function app()
	{
		if (!self::$app)
		{
			self::$app = new \IB\App;
		}
		return self::$app;
	}

	public static function getRootDir() : string
	{
		return self::$utilDir . '/../';
	}

	public static function getUtilDir() : string
	{
		return self::$utilDir;
	}

	public static function getPagesDir() : string
	{
		return self::$pagesDir . '/../pages';
	}
}

/* Enable autoloader */
spl_autoload_register('IB::autoload');
