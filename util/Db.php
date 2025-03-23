<?php
/**
 * Database management class
 */

namespace IB;

class Db
{
    protected $app = null;
    protected $conn = null;

	protected function __construct(&$app)
	{
		$this->app = $app;
	}

	/**
	 * Install the DDL if necessary
	 */
	protected function install()
	{
		$conn = $this->connect();
		$installed = true;
		try {
			$result = $conn->query('SELECT 1 FROM users LIMIT 1');
		} catch (\PDOException $e) {
			/* Throw an error if this fails unexpectedly */
			if ($conn->errorCode() !== '42S02') {
				$this->app->error($e->getMessage(), 'DDL check failed', $e->getTraceAsString());
			}
			/* The table is missing. DDL needs installation. */
			$installed = false;
		}
		if ($installed) {
			return;
		}
		$ddl_path = \IB::getRootDir() . '/sql/ddl.sql';
		if (!file_exists($ddl_path))
			$this->app->error('Unable to find DDL at expected path.', 'DDL installation failed', $ddl_path);
		try {
			$result = $conn->query(file_get_contents($ddl_path));
		} catch (\PDOException $e) {
			$this->app->error($e->getMessage(), 'DDL installation failed', $e->getTraceAsString());
		}
	}

	/**
	 * Get a database connection
	 */
	public function connect()
	{
		if ($this->conn)
			return $this->conn;

		/* Connect */
		$db = $this->app->config('db');
		if ($this->conn) {
			return;
		}
		$fmt = 'mysql:%s;port=%s;dbname=%s;charset=utf8mb4';
		$connstr = sprintf($fmt, $db['host'], $db['port'], $db['database']);
		try {
			$this->conn = new \PDO(
					$connstr,
					$db['user'], $db['pass']);
			$this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e) {
			$this->app->error($e->getMessage(), 'Database connection failed', $e->getTraceAsString());
		}

		/* Install DDL if necessary */
		$this->install();

		return $this->conn;
	}

	/**
	 * Connect to the database, prepare a query, and execute it.
	 */
	public function query($sql, $params)
	{
		try {
			$s = $this->connect()->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
			$s->execute($params);
			return $s->fetchAll();
		} catch (\PDOException $e) {
			$this->app->error($e->getMessage(), 'Query failed', "Query: $sql\n\n".$e->getTraceAsString());
		}
	}

	/**
	 * Execute an update query given a pile of parameters
	 */
	public function update(array | string $set,
			string $update, array | string $where) : array
	{
		$query = \IB\Db::buildUpdate($set, $update, $where);
		return $this->query($query['sql'], $query['params']);
	}

	/**
	 * Build a select query given a pile of parameters
	 */
	public static function buildUpdate(array | string $set,
			string $update, array | string $where) : array
	{
		/* UPDATE */
		$sql = array('UPDATE', $update);
		/* SET */
		$sql[] = 'SET';
		$params = array();
		if (gettype($set) === 'array') {
			$conditions = array();
			foreach ($set as $k => $v) {
				if (is_int($k)) {
					/* Add unnamed values directly */
					$conditions[] = $v;
				} else {
					/* Turn named values into parameters */
					$conditions[] = "$k = ?";
					$params[] = $v;
				}
			}
			$sql[] = implode(', ', $conditions);
		} else {
			$sql[] = $set;
		}
		/* WHERE */
		$sql[] = 'WHERE';
		if (gettype($where) === 'array') {
			$conditions = array();
			foreach ($where as $k => $v) {
				if (is_int($k)) {
					/* Add unnamed values directly */
					$conditions[] = $v;
				} else {
					/* Turn named values into parameters */
					$conditions[] = "$k = ?";
					$params[] = $v;
				}
			}
			$sql[] = implode(', ', $conditions);
		} else {
			$sql[] = $where;
		}
		return array('sql' => implode(' ', $sql), 'params' => $params);
	}

	/**
	 * Execute a select query given a pile of parameters
	 */
	public function select(array | string $values,
			array | string $from, array | string $where) : array
	{
		$query = \IB\Db::buildSelect($values, $from, $where);
		return $this->query($query['sql'], $query['params']);
	}

	/**
	 * Build a select query given a pile of parameters
	 */
	public static function buildSelect(array | string $values,
			array | string $from, array | string $where) : array
	{
		/* SELECT */
		$sql = array('SELECT');
		if (gettype($values) === 'array') {
			$sql[] = implode(', ', $values);
		} else {
			$sql[] = $values;
		}
		/* FROM */
		$sql[] = 'FROM';
		if (gettype($from) === 'array') {
			$sql[] = implode(', ', $from);
		} else {
			$sql[] = $from;
		}
		/* WHERE */
		$sql[] = 'WHERE';
		$params = array();
		if (gettype($where) === 'array') {
			$conditions = array();
			foreach ($where as $k => $v) {
				if (is_int($k)) {
					/* Add unnamed values directly */
					$conditions[] = $v;
				} else {
					/* Turn named values into parameters */
					$conditions[] = "$k = ?";
					$params[] = $v;
				}
			}
			$sql[] = implode(', ', $conditions);
		} else {
			$sql[] = $where;
		}
		return array('sql' => implode(' ', $sql), 'params' => $params);
	}

	public static function getInstance(&$app)
	{
		if ($app->hasClass(static::class))
			return $app->getClass(static::class);
		return new Db($app);
	}
}
