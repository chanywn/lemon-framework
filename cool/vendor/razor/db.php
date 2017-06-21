<?php
class db
{
	private static $host;
	private static $username;
	private static $password;
	private static $database;
	private static $pdo;
	private static $charset;

	private static $table;
	// length 2-3
	private static $where;
	// string
	private static $order;
	// length 2
	private static $limit;

	public static function connection()
	{
		include(__DIR__ . '/../../configs.php');

		self::$host     = $configs['mysql']['host'];
		self::$username = $configs['mysql']['username'];
		self::$password = $configs['mysql']['password'];
		self::$database = $configs['mysql']['database'];
		self::$charset  = $configs['mysql']['charset'];

		try {
			self::$pdo = new PDO(
				sprintf('mysql:host=%s;dbname=%s;charset=%s',
					self::$host,
					self::$database,
					self::$charset
				),
				self::$username, self::$password
			);
		} catch (PDOException $e) {
			error_log($e->getMessage(), 0);
			echo $configs['app']['app_env'] == 'local' ? $e->getMessage() : '';
			exit();
		}
	}

	public static function table($tableName)
	{
		self::$table = $tableName;
		return new self;
	}
	

	public function where($key, $value, $op = '=')
	{
		self::$where = [$key, $value, $op];
        return new self;
	}

	public function take($start,$num)
	{
		self::$limit = [$start, $num];
        return new self;
	}

	public function orderBy($key)
	{
		self::$order = $key;
        return new self;
	}

	private static function spellSelectSql($keys)
	{
		if(isset(self::$table)) {
			$select = "SELECT ". $keys;
			$from  = " FROM `". self::$table . "`";
			$where = isset(self::$where) ? " WHERE ". self::$where[0] . self::$where[2] .'"'. self::$where[1].'"' : '';
			$order = isset(self::$order) ? (" ORDER BY " . self::$order . " DESC") : '';
			$limit = isset(self::$limit) ? (" LIMIT " . self::$limit[0] . ",") . self::$limit[1] : '';
			return $select.$from.$where.$order.$limit;
		} else {
			return false;
		}
	}

	public function get($keys = '*')
	{
		$sql = self::spellSelectSql($keys);
		if($sql !== false) {
			$rows = self::fetchAll($sql);
			self::close();
			return $rows;
		}
		return false;
	}

	public function first($keys = '*')
	{
		$sql = self::spellSelectSql($keys);
		if($sql !== false) {
			$rows = self::fetchAll($sql);
			self::close();
			return $rows;
		}
		return false;
	}

	public function find($id, $key = 'id')
	{
		self::$where = [$key, $id, '='];
		$sql = self::spellSelectSql('*');
		if($sql !== false) {
			$rows = self::fetch($sql);
			self::close();
			return $rows;
		}
		return false;
	}

	public function save($arr)
	{
		$sql = '';
		$sql .="INSERT INTO `" .self::$table. "` ";
		$sql .="(`".implode("`,`",array_keys($arr))."`) "; 
		$sql .=" VALUES ";
		$sql .= "('".implode("','",$arr)."')";
		return self::insert($sql);
	}

	public function update($arr)
	{
		//UPDATE persondata SET age=age*2, age=age+1;
		$sql = '';
		$sql .="UPDATE `" .self::$table. "` SET ";
		foreach ($arr as $key => $value) {
			$sql .= '`'.$key.'`'. '=' ."'". $value ."',";
		}
		$sql = rtrim($sql, ",");
		
		$sql .= isset(self::$where) ? " WHERE ". self::$where[0] . self::$where[2] .'"'. self::$where[1].'"' : '';
		
		return self::insert(rtrim($sql, ","));
	}

	public static function insert($sql)
	{
		self::connection();
		$count = self::$pdo->exec($sql);
		self::close();
		return $count;
	}

	public static function fetchAll($sql)
	{
		self::connection();
		return  self::$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function fetch($sql)
	{
		self::connection();
		return self::$pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
	}

	public static function close()
	{
		self::$pdo = null;
	}
}