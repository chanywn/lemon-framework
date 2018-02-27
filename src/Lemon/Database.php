<?php
namespace Lemon;

class Database
{
	private static $driver;
	private static $host;
	private static $username;
	private static $password;
	private static $database;
	private static $pdo;
	private static $charset;

	private static $table;

	// private static $join;
	// length 2-3
	private static $where;

	private static $contain;

	private static $andWhere;
	// string
	private static $order;
	// length 2
	private static $limit;

	public static function connection()
	{
		try {
			self::$pdo = new \PDO(
				sprintf('%s:host=%s;dbname=%s;charset=%s',
					self::$driver,
					self::$host,
					self::$database,
					self::$charset
				),
				self::$username, self::$password
			);
			self::$pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING);
		} catch (PDOException $e) {
			error($e->getMessage());
			exit();
		}
	}

	public static function set($arr)
	{
		if(!isset($arr) || empty($arr)) {
			throw new \UnexpectedValueException("Database configs does not exist!");
		}
		self::$driver   = isset($arr['driver']) ?  $arr['driver'] : 'mysql';
		self::$host     = $arr['host'];
		self::$username = $arr['username'];
		self::$password = $arr['password'];
		self::$database = $arr['database'];
		self::$charset  = isset($arr['charset']) ? $arr['charset'] : 'utf-8';
	}
	public static function table($tableName)
	{
		self::$table = $tableName;
		return new self;
	}

	public function where($key, $value, $op = '=')
	{
		self::$where = [$key, addslashes($value), $op];
        return new self;
	}

	public function contain($key, $value)
	{
		self::$contain = [$key, addslashes($value)];
        return new self;
	}

	public function andWhere($key, $value, $op = '=')
	{
		self::$andWhere = [$key, addslashes($value), $op];
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
			$from  = " FROM `". self::$table . "` ";
			$where = isset(self::$where) ? " WHERE `". self::$where[0] .'` '. self::$where[2] .' "'. self::$where[1].'" ' : '';
			$andWhere = isset(self::$andWhere) ? " AND `". self::$andWhere[0] .'` '. self::$andWhere[2] .' "'. self::$andWhere[1].'" ' : '';
			$contain = isset(self::$contain) ? " WHERE ".self::$contain[0] . " LIKE '%" . self::$contain[1] ."%'" : '';
			$order = isset(self::$order) ? (" ORDER BY " . self::$order . " DESC") : '';
			$limit = isset(self::$limit) ? (" LIMIT " . self::$limit[0] . ",") . self::$limit[1] : '';
			$sql = $select.$from.$where.$andWhere.$contain.$order.$limit;
			self::reset();
			return $sql;
		} else {
			return false;
		}
	}

	private static function reset()
	{
		self::$table = null;
		self::$where = null;
		self::$andWhere = null;
		self::$contain = null;
		self::$order = null;
		self::$limit = null;
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
			$rows = self::fetch($sql);
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
		if(is_object($arr)){
			$arr = get_object_vars($arr);
		}

		foreach($arr as $key => $value){
			$arr[$key] = addslashes($value);
		}
		$sql = '';
		$sql .="INSERT INTO `" .self::$table. "` ";
		$sql .="(`".implode("`,`",array_keys($arr))."`) "; 
		$sql .=" VALUES ";
		$sql .= "('".implode("','",$arr)."')";
		self::reset();

		return self::exec($sql);
	}

	public function saveId($arr)
	{
		if(is_object($arr)){
			$arr = get_object_vars($arr);
		}
		
		foreach($arr as $key => $value){
			$arr[$key] = addslashes($value);
		}
		$sql = '';
		$sql .="INSERT INTO `" .self::$table. "` ";
		$sql .="(`".implode("`,`",array_keys($arr))."`) "; 
		$sql .=" VALUES ";
		$sql .= "('".implode("','",$arr)."')";

		self::reset();
		return self::execId($sql);
	}


	public function update($arr)
	{
		$sql = '';
		$sql .="UPDATE `" .self::$table. "` SET ";
		foreach ($arr as $key => $value) {
			if(is_numeric($value)){
				$sql .= '`'.$key.'`'. ' = ' . $value . ', ';
			} else {
				$sql .= '`'.$key.'`'. ' = ' .' "'. addslashes($value) .'", ';
			}
			
		}
		$sql = rtrim($sql, ", ");
		
		if(is_numeric(self::$where[1])){
			$sql .= isset(self::$where) ? " WHERE `". self::$where[0] .'` '. self::$where[2] .' '. self::$where[1].' ' : '';
		} else {
			$sql .= isset(self::$where) ? " WHERE `". self::$where[0] .'` '. self::$where[2] .' "'. self::$where[1].'" ' : '';
		}

		if(is_numeric(self::$andWhere[1])){
			$sql .= isset(self::$andWhere) ? " AND ". self::$andWhere[0] .' '. self::$andWhere[2] .' '. self::$andWhere[1].' ' : '';
		} else {
			$sql .= isset(self::$andWhere) ? " AND ". self::$andWhere[0] .' '. self::$andWhere[2] .' "'. self::$andWhere[1].'" ' : '';
		}

		self::reset();

		return self::exec($sql);
	}

	public function delete()
	{
		$sql = '';
		$sql .="DELETE FROM `" .self::$table. "`";
		$sql .= isset(self::$where) ? " WHERE ". self::$where[0] . self::$where[2] .'"'. self::$where[1].'"' : '';
		$sql .= isset(self::$andWhere) ? " AND ". self::$andWhere[0] . self::$andWhere[2] .'"'. self::$andWhere[1].'"' : '';

		self::reset();
		
		return self::exec($sql);
	}

	public static function exec($sql)
	{
		self::connection();
		$countOrFlase = self::$pdo->exec($sql);
		self::close();
		return $countOrFlase;
	}

	public static function execId($sql)
	{
		$lastInsertId = false;
		self::connection();
		$countOrFlase = self::$pdo->exec($sql);
		if($countOrFlase !== false) {
			$lastInsertId = self::$pdo->lastInsertId();
		}
		self::close();
		return $lastInsertId;
	}


	public static function fetchAll($sql)
	{
		self::connection();
		return self::$pdo->query($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public static function fetch($sql)
	{
		self::connection();
		return self::$pdo->query($sql)->fetch(\PDO::FETCH_OBJ);
	}

	public static function close()
	{
		self::$pdo = null;
	}
}