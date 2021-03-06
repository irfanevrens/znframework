<?php
class MysqlDriver
{
	private $config;
	private $connect;
	private $query;
	public function connect($config = array())
	{
		$this->config = $config;
		$this->connect = 	($this->config['pconnect'] === true)
							? @mysql_pconnect($this->config['host'], $this->config['user'], $this->config['password'])
							: @mysql_connect($this->config['host'], $this->config['user'], $this->config['password']);
							
		if( empty($this->connect) ) die(get_message('Database', 'db_mysql_connect_error'));
	
		mysql_select_db($this->config['database'], $this->connect);
		
		
		if($this->config['charset'])   
			$this->query("SET NAMES '".$this->config['charset']."'", $this->connect);
		if($this->config['charset'])   
			$this->query('SET CHARACTER SET '.$this->config['charset'], $this->connect);	
		if($this->config['collation']) 
			$this->query('SET COLLATION_CONNECTION = "'.$this->config['collation'].'"', $this->connect);
	}	
	
	public function exec($query)
	{
		return mysql_query($query, $this->connect);
	}
	
	public function query($query)
	{
		 $this->query = mysql_query($query, $this->connect);
		 return $this->query;
	}
	
	public function trans_start()
	{
		$this->query('SET AUTOCOMMIT=0');
		$this->query('START TRANSACTION');
		return true;
	}
	
	public function trans_rollback()
	{
		$this->query('ROLLBACK');
		$this->query('SET AUTOCOMMIT=1');
		return TRUE;
	}
	
	public function trans_commit()
	{
		$this->query('COMMIT');
		$this->query('SET AUTOCOMMIT=1');
		return true;
	}
	
	public function list_databases()
	{
		return false;
	}
	
	public function list_tables()
	{
		return false;
	}
	
	public function insert_id()
	{
		if( ! empty($this->connect))
			return mysql_insert_id($this->connect);
		else
			return false;
	}
	
	public function column_data()
	{
		if( empty($this->query)) return false;
		
		$columns = array();
		for ($i = 0, $c = $this->num_fields(); $i < $c; $i++)
		{
			$columns[$i]					= new stdClass();
			$columns[$i]->name				= mysql_field_name($this->query, $i);
			$columns[$i]->type				= mysql_field_type($this->query, $i);
			$columns[$i]->max_length		= mysql_field_len($this->query, $i);
			$columns[$i]->primary_key		= (int) (strpos(mysql_field_flags($this->query, $i), 'primary_key') !== false);
		}
		return $columns;
	}
	
	public function backup($filename = ''){ return false; }
	
	public function truncate($table = ''){ return false; }
	
	public function add_column(){ return false; }
	
	public function drop_column(){ return false; }
	
	public function rename_column(){ return false; }
	
	public function modify_column(){ return false; }
	
	public function num_rows()
	{
		if( ! empty($this->query))
			return mysql_num_rows($this->query);	
		else
			return 0;
	}
	
	public function columns()
	{
		if( empty($this->query)) return false;
		$columns = array();
		$num_fields = $this->num_fields(); 
		for($i=0; $i < $num_fields; $i++)
		{	
				$columns[] = mysql_field_name($this->query,$i);
		}
		
		return $columns;
	}
	
	public function num_fields()
	{
		if( ! empty($this->query))
			return mysql_num_fields($this->query);
		else
			return 0;
	}
	
	public function result()
	{
		if( empty($this->query)) return false;
		$rows = array();
		while($data = mysql_fetch_assoc($this->query))
		{
			$rows[] = (object)$data;
		}
		
		return $rows;
	}
	
	public function result_array()
	{
		if( empty($this->query)) return false;
		$rows = array();
		while($data = mysql_fetch_assoc($this->query))
		{
			$rows[] = $data;
		}
		
		return $rows;
	
	}
	
	public function row()
	{
		if( empty($this->query)) return false;
		$data = mysql_fetch_assoc($this->query);
		return (object)$data;
	}
	
	public function real_escape_string($data = '')
	{
		if( empty($this->connect)) return false;
		return mysql_real_escape_string($data, $this->connect);
	}
	
	public function error()
	{
		if( ! empty($this->connect))
			return mysql_error($this->connect);
		else
			return false;
	}
	
	public function fetch_row()
	{
		if( ! empty($this->query))
			return mysql_fetch_row($this->query);
		else
			return 0;	
	}
	
	public function fetch_array()
	{
		if( ! empty($this->query))
			return mysql_fetch_array($this->query);
		else
			return false;	
	}
	
	public function fetch_assoc()
	{
		if( ! empty($this->query))
			return mysql_fetch_assoc($this->query);
		else
			return false;	
	}
	
	public function affected_rows()
	{
		if( ! empty($this->connect))
			return mysql_affected_rows($this->connect);
		else
			return false;	
	}
	
	public function close()
	{
		if( ! empty($this->connect)) @mysql_close($this->connect); else return false;
	}	
	
	public function version()
	{
		if( ! empty($this->connect)) return mysql_get_server_info($this->connect);
	}	
}