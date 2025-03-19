<?php
/**
 * App class
 */

namespace IB;

class App
{
	protected $data;

	public function __construct()
	{
		$this->data = array();
		$this->classes = array();
		$this->initialize();
	}

	/**
	 * Load initial data required for the app to work.
	 */
	protected function initialize() : void
	{
		/* Load the configuration files */
		$file = \IB::getRootDir() . '/config.sample.php';
		$config = array();
		if (file_exists($file)) {
			require($file);
		}
		$file = \IB::getRootDir() . '/config.php';
		if (file_exists($file)) {
			require($file);
		} else {
			$this->error("The configuration file is missing.\nPlease create a config.php file in the root directory of the app.");
		}
		$this->data['config'] = $config;

		/* Configure PHP */
		ini_set('display_errors', $this->data['config']['debug'] ? 1 : 0);
	}

	/**
	 * Display a formatted error message.
	 */
	public function error(string $message, string $title=null, string $extra=null) : void
	{
		/* Output */
		$out = '';
		if (!empty($title)) {
			$out .= '<b>'.htmlspecialchars($title)."</b><br>\n";
		}
		$out .= htmlspecialchars($message);
		if (!empty($extra) && $this->config('debug')) {
			$out .= "\n<pre>".htmlspecialchars($extra).'</pre>';
		}
		/* Simple page */
		header('Content-Type: text/html');
		echo '<!DOCTYPE html><html lang="en"><meta charset="utf-8"><title>Error</title>';
		echo '<style>body { text-align: center; font-family: sans-serif; background: #ddd; }</style>';
		echo '<style>pre { text-align: left; }</style>';
		echo '<style>.error { display: inline-block; border: 2px solid #c22; border-radius: 1rem; background: #fdd; padding: 2rem; margin: 2rem; }</style>';
		echo '<style>.error pre { border: 2px solid #c22; border-radius: 1rem; padding: 1rem;}</style>';
		echo "<div class=\"error\">$out</div>";
		die();
	}

	public function config(string $key) {
		return $this->data['config'][$key];
	}

	public function hasClass(string $name) : bool
	{
		return isset($this->data['class'][$name]);
	}

	public function getClass(string $name)
	{
		if (!$this->hasClass($name))
		{
			$this->data['class'][$name] = $name::getInstance($this);
		}
		return $this->data['class'][$name];
	}
}
