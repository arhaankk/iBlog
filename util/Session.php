<?php
/**
 * Session handling
 */

namespace IB;

class Session
{
    protected $app = null;

	protected function __construct(&$app)
	{
		$this->app = $app;
		$this->user = null;
		$this->users = $app->getClass('IB\Users');
		/* Manually start a PHP session support */
		\session_start();
	}

	/**
	 * Get the user who is logged in
	 */
	public function getUser() : array | null {
		if ($this->user)
			return $this->user;
		if (!isset($_SESSION['user_id']))
			return null;
		$id = $_SESSION['user_id'];
		$users = $this->users->get(['id' => $id]);
		if (count($users) < 1)
			return null;
		$this->user = $users[0];
		return $this->user;
	}

	/**
	 * Check if the user is authenticated
	 */
	public function isAuthenticated() : bool {
		return !is_null($this->getUser());
	}

	/**
	 * Check if the user is an admin
	 */
	public function isAdmin() : bool {
		if (!$this->isAuthenticated())
			return false;
		/* TODO: Make admin status configurable */
		return $this->getUser()['username'] === 'alice';
	}

	public static function getInstance(&$app)
	{
		if ($app->hasClass(static::class))
			return $app->getClass(static::class);
		return new Session($app);
	}
}
