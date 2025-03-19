<?php
/**
 * User management class
 */

namespace IB;

class Users
{
    protected $app = null;
    protected $pdo = null;

	protected function __construct(&$app)
	{
		$this->app = $app;
		$this->db = $app->getClass('IB\Db');
	}

	/**
	 * Get an array of users given an array of parameters
	 */
	public function get(array $params)
	{
		$r = $this->db->select('*', 'users', $params);
		return $r;
	}

	public static function getInstance(&$app)
	{
		if ($app->hasClass(static::class))
			return $app->getClass(static::class);
		return new Users($app);
	}
}
