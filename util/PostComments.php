<?php
/**
 * Post comment management class
 */

namespace IB;

class PostComments
{
    protected $app = null;
    protected $pdo = null;

	protected function __construct(&$app)
	{
		$this->app = $app;
		$this->db = $app->getClass('IB\Db');
	}

	/**
	 * Get an array of comments given an array of parameters
	 */
	public function get(array $params, string | null $order='created_at DESC', int | null $limit=null)
	{
		$r = $this->db->select('*', 'postComments', $params, $order);
		return $r;
	}

	/**
	 * Add a post comment to the database
	 */
	public function add(array $params)
	{
		$r = $this->db->insert('postComments', $params);
		return $r;
	}

	public static function getInstance(&$app)
	{
		if ($app->hasClass(static::class))
			return $app->getClass(static::class);
		return new PostComments($app);
	}
}
