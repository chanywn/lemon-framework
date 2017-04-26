<?php
namespace razor;

class  mysql 
{
	private $host;
	private $username;
	private $password;
	private $database;
	private $mysqli;
	private $sql;

	public function __construct()
	{
		$configs = require_once __DIR__ . '/../../configs.php';
		$this->host     = $configs['mysql']['host'];
		$this->username = $configs['mysql']['username'];
		$this->password = $configs['mysql']['password'];
		$this->database = $configs['mysql']['database'];
		$this->conn();
		$this->sql = ["from"=>"","where"=>"","order"=>"","limit"=>""];
	}

	public function conn()
	{
		$this->mysqli = new \mysqli($this->host,$this->username, $this->password, $this->database);
		$this->mysqli->query("set names utf8");
	}

	public function create($sql, $arrs = false)
	{
		if($arrs !== false) {
			foreach ($arrs as $arr) {
				$sql = substr_replace($sql, $arr, strpos($sql, "?"), 1);
			}
		} 

		$this->mysqli->query($sql);
		$this->close();
	}

	public function select($sql, $arrs = false)
	{
		if($arrs !== false) {
			foreach ($arrs as $arr) {
				$sql = substr_replace($sql, $arr, strpos($sql, "?"), 1);
			}
		} 

		$result = $this->mysqli->query($sql);

		if($result === false){
			return false;
		}

		$data = $result->fetch_all(MYSQLI_ASSOC);
		$this->close();

		return $data;
	}


	public function table($tableName)
	{
		$this->sql['from']='FROM `' . $tableName.'`';
		return $this;
	}

	public function orderBy($id)
	{
		$this->sql['order']='ORDER BY ' . $id . ' DESC';
        return $this;
	}

	public function where($k, $v, $op = '=')
	{
		$this->sql['where']='WHERE '.$k.$op.$v;
        return $this;
	}

	public function take($skip,$num) 
	{
		$this->sql['limit']='LIMIT ' . $skip . ',' . $num;
        return $this;
	} 

	public function first($_select = '*')
	{
		$sql = 'SELECT '.$_select.' '.(implode(' ',$this->sql));
		$data = $this->select($sql);
		return $data[0];
	}
	public function find($id,$key = 'id')
	{
		$this->where($key,$id);
		$sql = 'SELECT * '.(implode(' ',$this->sql));
		$data = $this->select($sql);
		return $data[0];
	}

	public function get($_select = '*')
	{
		$sql = 'SELECT '.$_select.' '.(implode(' ',$this->sql));
		return $this->select($sql);
	}

	public function insert($table,$arr)
	{
		
		
	}
	// DB::table('users')->insert([]);
	// DB::table('users')->where('id', 1)->update(['votes' => 1]);

	// DB::table('users')->where('id', 1)->delete();

	//insert into table(`title`, `content`) values('?', '?')
	//update table set 年龄=18 where 姓名='蓝色小名'
	//delete from table where name='s'
	public function close()
	{
		$this->mysqli->close();
	}
}