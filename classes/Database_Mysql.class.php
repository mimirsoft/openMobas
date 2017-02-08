<?php
/*
 * 
    This file is part of OpenMobas
    Copyright (C) 2011, Kevin Milhoan

    OpenMobas is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    OpenMobas is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with OpenMobas.  If not, see <http://www.gnu.org/licenses/>.

   Contact MimirSoft at mimirsoft@gmail.com or www.mimirsoft.com

*
*/
class MysqlException extends Exception { 
  public $backtrace;
  public $message;
  public function __construct($message=false, $code=false, $link) {
    if(!$message) {
      $this->message = mysqli_error($link);
    }
    else {
    	$this->message = $message.mysqli_error($link);
    }
    if(!$code) {
      $this->code = mysqli_errno($link);
    }
    $this->backtrace = debug_backtrace();
  }
}

interface DB_Connection {
  public function prepare($query);
  //public function execute($query);
}

interface DB_Statement {
  public function execute();
  public function bind_param($key, $value);
  public function fetch_row();
  public function fetch_assoc();
  public function fetchall_assoc();
}

class DB_Mysql implements DB_Connection {
  protected $user;
  protected $pass;
  protected $dbhost;
  protected $dbname;
  protected $dbh;

  
  
  public function __construct($DB_SETTINGS) 
  {
    $this->user = $DB_SETTINGS['user'];
    $this->pass = $DB_SETTINGS['pass'];
    $this->dbhost = $DB_SETTINGS['dbhost'];
    $this->dbname = $DB_SETTINGS['dbname'];
  }
  
  protected function connect() 
  {
    $this->dbh = new mysqli($this->dbhost, $this->user, $this->pass, $this->dbname);
    
    if($this->dbh->connect_error) {
      throw new MysqlException("No connection for USER ".$this->user." to DB ".$this->dbname." using password ".$this->pass, "", $this->dbh);
    }
    //if(!mysql_select_db($this->dbname, $this->dbh)) {
     // throw new MysqlException;
    //}
  }
  
  /*public function execute($query) 
  {
    if(!$this->dbh) {
      $this->connect();
    }
    $ret= $this->dbh->query($query);
    //$ret = mysql_query($query, $this->dbh); 
    if($ret === false) {
      throw new MysqlException;
    }
    else if(!is_resource($ret)) {
      return TRUE;
    } else {
      $stmt = new DB_MysqlStatement($this->dbh, $query);
      $stmt->result = $ret;
      return $stmt;
    }
  }
  */
  
  
  public function prepare($query) 
  {
    if(!$this->dbh) {
      $this->connect();
    }
    return new DB_MysqlStatement($this->dbh, $query);
  }
}

class DB_MysqlStatement implements DB_Statement {
  public $result;
  public $binds;
  public $query;
  public $dbh;
  public function __construct($dbh, $query) {
    $this->query = $query;
    $this->dbh = $dbh;
    if($this->dbh->connect_error) {
      throw new MysqlException("Not a valid database connection", "", $this->dbh);
    }
  }
  public function bind_param($ph, $pv) {
    $this->binds[$ph] = $pv;
    return $this;
  }
  public function execute() {
    $binds = func_get_args();
    foreach($binds as $index => $name) {
      $this->binds[$index + 1] = $name;
    }
    $cnt = count($binds);
    $query = $this->query;
    if ($this->binds) 
    { 
        foreach ($this->binds as $ph => $pv) 
        {
            $query = str_replace(":$ph:", "'".$this->dbh->real_escape_string($pv)."'", $query);
        }
    }
    $this->query = $query;
    $this->result = $this->dbh->query($query);
    if($this->result === false) {
 	  echo $query;
      throw new MysqlException("query result === false", "", $this->dbh);
    }
    return $this;
  }
  
  public function fetch_row() 
  {
    if(!$this->result) {
      throw new MysqlException("Query not executed", "", $this->dbh);
    }
    return mysql_fetch_row($this->result);
  }
  
  public function fetch_assoc() 
  {
    return $this->result->fetch_assoc();
  }
  
  public function num_rows() 
  {
    return $this->result->num_rows;
  }
  
  public function fetchall_assoc() {
    $retval = array();
    if(!$this->result)//if this set is not empty
    {
    	return;
    }
    while($row = $this->fetch_assoc()) {
      $retval[] = $row;
    }
    return $retval;
  }
  public function fetchall_row() {
    $retval = array();
    while($row = $this->fetch_row()) {
      $retval[] = $row;
    }
    return $retval;
  }
   public function insert_id(){

        // connect to the database
        // $link = $this->connect();
        // Get the ID generated from the previous INSERT operation
        $last_id = mysqli_insert_id($this->dbh);
        // return last ID
        return $last_id;

    }
}

class DB_Result {
  protected $stmt;
  protected $result = array();
  private $rowIndex = 0;
  private $currIndex = 0;
  private $done = false;
 
  public function __construct(DB_Statement $stmt) 
  {
    $this->stmt = $stmt;
  } 
  public function first() 
  {
    if(!$this->result) {
      $this->result[$this->rowIndex++] = $this->stmt->fetch_assoc();
    }
    $this->currIndex = 0;
    return $this;
  }
  public function last()
  {
    if(!$this->done) {
      array_push($this->result, $this->stmt->fetchall_assoc());
    }
    $this->done = true;
    $this->currIndex = $this->rowIndex = count($this->result) - 1;
    return $this;
  }
  public function next()
  {
    if($this->done) {
      return false;
    }
    $offset = $this->currIndex + 1;
    if(!$this->result[$offset]) {
      $row = $this->stmt->fetch_assoc();
      if(!$row) {
        $this->done = true;
        return false;
      }
      $this->result[$offset] = $row;
      ++$this->rowIndex;
      ++$this->currIndex;
      return $this;
    }
    else {
      ++$this->currIndex;
      return $this;
    }
  }
  public function prev()
  {
    if($this->currIndex == 0) {
      return false;
    }
    --$this->currIndex;
    return $this;
  }
  public function __get($value) 
  {
    if(array_key_exists($value, $this->result[$this->currIndex])) {
      return $this->result[$this->currIndex][$value];
    }
  }
} 

class DB_Mysql_Test extends DB_Mysql {
    protected $user   = "test";
    protected $pass   = "test";
    protected $dbhost = "localhost";
    protected $dbname = "test";

    public function __construct() { }
} 

class DB_Mysql_Test_Debug extends DB_Mysql_Test {
  protected $elapsedTime;
  public function execute($query) {
    // set timer;
    parent::execute($query);
    // end timer;
  }
  public function getElapsedTime() {
    return $this->$elapsedTime;
  }
}

class DB_Mysql_Admin extends DB_Mysql {

    public function __construct() 
    { 
        global $DB_ADMIN_SETTINGS;
        $this->user = $DB_ADMIN_SETTINGS['user'];
        $this->pass = $DB_ADMIN_SETTINGS['pass'];
        $this->dbhost = $DB_ADMIN_SETTINGS['dbhost'];
        $this->dbname = $DB_ADMIN_SETTINGS['dbname'];
    }
} 
?>
