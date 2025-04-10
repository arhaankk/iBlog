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
	 * Get an array of posts given an array of parameters
	 */
	public function get(array $params, string | null $order='created_at DESC', int | null $limit=null)
	{
		$r = $this->db->select('*', 'blog', $params, $order);
		return $r;
	}

	/**
	 * Delete a post given conditions
	 */
	public function delete(array $params, string | null $order='created_at DESC', int | null $limit=null)
	{
		$r = $this->db->delete('blog', $params, $order);
		return $r;
	}

	/**
	 * Add a view to a post if one does not exist.
	 * Prevention of multiple inserts is handled by the database constraints.
	 */
	public function addView(int $postId, int $userId)
	{
		$params = ['blogId' => $postId, 'userId' => $userId];
		$ignore = [23000]; /* Integrity constraint violation */
		$r = $this->db->insert('postViews', $params, ignore: $ignore);
		return $r;
	}

	/**
	 * Add a post to the database and return the post ID.
	 */
	public function add(array $params)
	{
		return $this->db->insert('blog', $params);
	}

	/**
	 * Validate a post.
	 */
	public function validate(array $post): string | null
	{

		/* Required fields */
		$fields = ['title', 'topic', 'content', 'userId'];
		foreach ($fields as $field)
			if (!isset($post[$field]))
				return "Missing required field $field";
		/* Topics */
		$topics = $this->app->config('topics');
		if (!in_array($post['topic'], $topics))
			return 'Invalid topic';
		return null;
	}

	public static function getInstance(&$app)
	{
		if ($app->hasClass(static::class))
			return $app->getClass(static::class);
		return new Posts($app);
	}
}
