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

	private static $join;
	// length 2-3
	private static $where;

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
	
	public static function join($tableName)
	{
		self::$join = $tableName;
		return new self;
	}

	public function where($key, $value, $op = '=')
	{
		self::$where = [$key, addslashes($value), $op];
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
			$join  = isset(self::$join) ? " ,`". self::$join . "` " : '';
			$where = isset(self::$where) ? " WHERE ". self::$where[0] .' '. self::$where[2] .' '. self::$where[1].' ' : '';
			$andWhere = isset(self::$andWhere) ? " AND ". self::$andWhere[0] .' '. self::$andWhere[2] .' '. self::$andWhere[1].' ' : '';
			$order = isset(self::$order) ? (" ORDER BY " . self::$order . " DESC") : '';
			$limit = isset(self::$limit) ? (" LIMIT " . self::$limit[0] . ",") . self::$limit[1] : '';
			return $select.$from.$join.$where.$andWhere.$order.$limit;
		} else {
			return false;
		}
	}

	private static function spellSelectCountSql($keys)
	{
		if(isset(self::$table)) {
			$select = "SELECT COUNT(" . $keys . ")";
			$from  = " FROM `". self::$table . "`";
			$where = isset(self::$where) ? " WHERE ". self::$where[0] . self::$where[2] .'"'. self::$where[1].'"' : '';
			$andWhere = isset(self::$andWhere) ? " AND ". self::$andWhere[0] . self::$andWhere[2] .'"'. self::$andWhere[1].'"' : '';
			$order = isset(self::$order) ? (" ORDER BY " . self::$order . " DESC") : '';
			$limit = isset(self::$limit) ? (" LIMIT " . self::$limit[0] . ",") . self::$limit[1] : '';
			return $select.$from.$where.$andWhere.$order.$limit;
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
			$rows = self::fetch($sql);
			self::close();
			return $rows;
		}
		return false;
	}

	public function count($keys = '*')
	{
		$sql = self::spellSelectCountSql($keys);
		if($sql !== false) {
			$rows = self::fetch($sql);
			self::close();
			return $rows['COUNT(*)'];
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
		foreach($arr as $key => $value){
			$arr[$key] = addslashes($value);
		}
		$sql = '';
		$sql .="INSERT INTO `" .self::$table. "` ";
		$sql .="(`".implode("`,`",array_keys($arr))."`) "; 
		$sql .=" VALUES ";
		$sql .= "('".implode("','",$arr)."')";
		return self::exec($sql);
	}

	public function saveId($arr)
	{
		foreach($arr as $key => $value){
			$arr[$key] = addslashes($value);
		}
		$sql = '';
		$sql .="INSERT INTO `" .self::$table. "` ";
		$sql .="(`".implode("`,`",array_keys($arr))."`) "; 
		$sql .=" VALUES ";
		$sql .= "('".implode("','",$arr)."')";
		return self::execId($sql);
	}


	public function update($arr)
	{
		//UPDATE persondata SET age=age*2, age=age+1;
		$sql = '';
		$sql .="UPDATE `" .self::$table. "` SET ";
		foreach ($arr as $key => $value) {
			$sql .= '`'.$key.'`'. '=' ."'". addslashes($value) ."',";
		}
		$sql = rtrim($sql, ",");
		
		$sql .= isset(self::$where) ? " WHERE ". self::$where[0] .' '. self::$where[2] .' '. self::$where[1].' ' : '';

		$sql .= isset(self::$andWhere) ? " AND ". self::$andWhere[0] .' '. self::$andWhere[2] .' '. self::$andWhere[1].' ' : '';
		
		return self::exec($sql);
	}

	public function delete()
	{
		//DELETE FROM 表名称 WHERE 列名称 = 值 
		$sql = '';
		$sql .="DELETE FROM `" .self::$table. "`";
		$sql .= isset(self::$where) ? " WHERE ". self::$where[0] . self::$where[2] .'"'. self::$where[1].'"' : '';
		$sql .= isset(self::$andWhere) ? " AND ". self::$andWhere[0] . self::$andWhere[2] .'"'. self::$andWhere[1].'"' : '';
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
		// debug($sql);
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