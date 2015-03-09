<?php
class EyeMySQLAdap
{
	private $host, $user, $pass, $db_name;
	private $link;
	private $result;
	const DATETIME = 'Y-m-d H:i:s';
	const DATE = 'Y-m-d';

	public function __construct($host, $user, $password, $db, $persistant = false, $connect_now = true)
	{
		$this->host = $host; // Host address
		$this->user = $user;	// User
		$this->pass = $password;	// Password
		$this->db_name = $db;	// Database

		if ($connect_now)
			$this->connect($persistant);

		return;
	}

	public function __destruct()
	{
		$this->close();
	}

	public function connect($persist = false)
	{
		if ($persist)
			$link = mysql_pconnect($this->host, $this->user, $this->pass);
		else
			$link = mysql_connect($this->host, $this->user, $this->pass);

		if (!$link)
			trigger_error('Could not connect to the database.', E_USER_ERROR);

		if ($link)
		{
			$this->link = $link;
			if (mysql_select_db($this->db_name, $link))
				return true;
		}

		return false;
	}

	public function query($query)
	{
		$result = mysql_query($query, $this->link);

		$this->result = $result;

		if ($result == false)
			trigger_error('Uncovered an error in your SQL query script: "' . $this->error() . '"');

		return $this->result;
	}

	public function update(array $values, $table, $where = false, $limit = false)
	{
		if (count($values) < 0)
			return false;
			
		$fields = array();
		foreach($values as $field => $val)
			$fields[] = "`" . $field . "` = '" . $this->escapeString($val) . "'";

		$where = ($where) ? " WHERE " . $where : '';
		$limit = ($limit) ? " LIMIT " . $limit : '';

		if ($this->query("UPDATE `" . $table . "` SET " . implode($fields, ", ") . $where . $limit))
			return true;
		else
			return false;
	}

	public function insert(array $values, $table)
	{
		if (count($values) < 0)
			return false;
		
		foreach($values as $field => $val)
			$values[$field] = $this->escapeString($val);

		if ($this->query("INSERT INTO `" . $table . "`(`" . implode(array_keys($values), "`, `") . "`) VALUES ('" . implode($values, "', '") . "')"))
			return true;
		else
			return false;
	}

	public function select($fields, $table, $where = false, $orderby = false, $limit = false)
	{
		if (is_array($fields))
			$fields = "`" . implode($fields, "`, `") . "`";

		$orderby = ($orderby) ? " ORDER BY " . $orderby : '';
		$where = ($where) ? " WHERE " . $where : '';
		$limit = ($limit) ? " LIMIT " . $limit : '';

		$this->query("SELECT " . $fields . " FROM " . $table . $where . $orderby . $limit);

		if ($this->countRows() > 0)
		{
			$rows = array();

			while ($r = $this->fetchAssoc())
				$rows[] = $r;

			return $rows;
		} else
			return false;
	}

	public function selectOne($fields, $table, $where = false, $orderby = false)
	{
		$result = $this->select($fields, $table, $where, $orderby, '1');

		return $result[0];
	}
	
	public function selectOneValue($field, $table, $where = false, $orderby = false)
	{
		$result = $this->selectOne($field, $table, $where, $orderby);

		return $result[$field];
	}

	public function delete($table, $where = false, $limit = 1)
	{
		$where = ($where) ? " WHERE " . $where : '';
		$limit = ($limit) ? " LIMIT " . $limit : '';

		if ($this->query("DELETE FROM `" . $table . "`" . $where . $limit))
			return true;
		else
			return false;
	}

	public function fetchAssoc($query = false)
	{
		$this->resCalc($query);
		return mysql_fetch_assoc($query);
	}

	public function fetchRow($query = false)
	{
		$this->resCalc($query);
		return mysql_fetch_row($query);
	}

	public function fetchOne($query = false)
	{
		list($result) = $this->fetchRow($query);
		return $result;
	}

	public function fieldName($query = false, $offset)
	{
		$this->resCalc($query);
		return mysql_field_name($query, $offset);
	}

	public function fieldNameArray($query = false)
	{
		$names = array();

    	$field = $this->countFields($query);

    	for ( $i = 0; $i < $field; $i++ )
			$names[] = $this->fieldName($query, $i);

		return $names;
	}

	public function freeResult()
	{
		return mysql_free_result($this->result);
	}

	public function escapeString($str)
	{
		return mysql_real_escape_string($str, $this->link);
	}

	public function countRows($result = false)
	{
		$this->resCalc($result);
		return (int) mysql_num_rows($result);
	}

	public function countFields($result = false)
	{
		$this->resCalc($result);
		return (int) mysql_num_fields($result);
	}

	public function insertId()
	{
		return (int) mysql_insert_id($this->link);
	}

	public function affectedRows()
	{
		return (int) mysql_affected_rows($this->link);
	}

	public function error()
	{
		return mysql_error($this->link);
	}

	public function dumpInfo()
	{
		echo mysql_info($this->link);
	}

	public function close()
	{
		return mysql_close($this->link);
	}

	private function resCalc(&$result)
	{
		if ($result == false)
			$result = $this->result;
		else {
			if (gettype($result) != 'resource')
				$result = $this->query($result);
		}

		return;
	}
}