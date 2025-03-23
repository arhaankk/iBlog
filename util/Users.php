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

	/**
	 * Fetch a profile picture from an ID.
	 */
	public function getImage(int $id): array | null
	{
		$query = \IB\Db::buildSelect('profile_img', 'users', ['id' => $id]);
		try {
			$s = $this->db->connect()->prepare($query['sql'], [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
			$s->execute($query['params']);
			$s->bindColumn(1, $lob, \PDO::PARAM_LOB);
			if (!$s->fetch() || empty($lob))
				return null;
			$data = stream_get_contents($lob);
		} catch (\PDOException $e) {
			$sql = $query['sql'];
			$this->app->error($e->getMessage(), 'Profile picture fetch failed', "Query: $sql\n\n".$e->getTraceAsString());
		}

		/* Guess media type */
		$mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($data);
		if (strncmp($mime, "image/", 6) !== 0) {
			$this->app->error('Profile picture has invalid header.', 'Failed to display avatar', "Type: $mime\nUser ID: $id");
		}

		return array('mime' => $mime, 'data' => $data);
	}

	public static function getInstance(&$app)
	{
		if ($app->hasClass(static::class))
			return $app->getClass(static::class);
		return new Users($app);
	}
}
