<?php
/**
 * Post management class
 */

namespace IB;

class Posts
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
	public function get(array $params, string | null $order='created_at DESC', int | null $limit=null)
	{
		$r = $this->db->select('*', 'blog', $params, $order);
		return $r;
	}

	public static function getInstance(&$app)
	{
		if ($app->hasClass(static::class))
			return $app->getClass(static::class);
		return new Posts($app);
	}
}
