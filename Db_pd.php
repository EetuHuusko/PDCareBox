<?php

include ("dbconf_pd.php");
	
/**
* Database wrapper for a MySQL with PHP tutorial
*
* @copyright Eran Galperin
* @license MIT License
* @see http://www.binpress.com/tutorial/using-php-with-mysql-the-right-way/17
*/
class Db {



	// The database connection
	protected static $connection;
	/**
	* Connect to the database
	*
	* @return bool false on failure / mysqli MySQLi object instance on success
	*/
	public function connect() {
		// Try and connect to the database
		if(!isset(self::$connection)) {
		// TODO: Put the configuration file outside of the document root
		self::$connection = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        mysqli_set_charset(self::$connection,"utf8");
		}
		// If connection was not successful, handle the error
		if(self::$connection === false) {
		// Handle error - notify administrator, log to a file, show an error screen, etc.
		return false;
		}
		return self::$connection;
	}
	/**
	* Query the database
	*
	* @param $query The query string
	* @return mixed The result of the mysqli::query() function
	*/
	public function query($query) {
		// Connect to the database
		$connection = $this -> connect();
		// Query the database
		$result = $connection -> query($query);
		return $result;
	}
	/**
	* Fetch rows from the database (SELECT query)
	*
	* @param $query The query string
	* @return bool False on failure / array Database rows on success
	*/
	public function select($query) {
		$rows = array();
		$result = $this -> query($query);
		if($result === false) {
		return false;
		}
		while ($row = $result -> fetch_assoc()) {
		$rows[] = $row;
		}
		return $rows;
	}
	/**
	* Fetch the last error from the database
	*
	* @return string Database error message
	*/
	public function error() {
		$connection = $this -> connect();
		return $connection -> error;
	}
	/**
	* Quote and escape value for use in a database query
	*
	* @param string $value The value to be quoted and escaped
	* @return string The quoted and escaped string
	*/
	public function quote($value) {
		$connection = $this -> connect();
		return "'" . mysqli_real_escape_string($connection, $value) . "'";
	}
	
	public function quoteNum($value) {
		$connection = $this -> connect();
		return mysqli_real_escape_string($connection, $value);
	}
    
	public function inserted_id() {
		$connection = $this -> connect();
		return $connection->insert_id;
	}
    
    //return 0 if not, 1 if yes
    public function user_exists($user_id){
        $rows = array();
		$result = $this -> query("SELECT user_id FROM users WHERE user_id = '$user_id'");
		if($result === false) {
            return false;
		}
		while ($row = $result -> fetch_assoc()) {
            $rows[] = $row;
		}
        if(count($rows) >= 1) {
            return 1;
        } else if(count($rows) == 0) {
            return 0;
        } else {
            return false;
        }
    }

    
    //return 0 if not, 1 (or N many times) if yes
    public function get_ucontributionscount($user_id){
        $rows = array();
		$result = $this -> query("SELECT * FROM options WHERE user_id = '$user_id'");
		if($result === false) {
            return false;
		}
		while ($row = $result -> fetch_assoc()) {
            $rows[] = $row;
		}
        if(count($rows) >= 1) {
            return count($rows);
        } else if(count($rows) == 0) {
            return 0;
        } else {
            return false;
        }
    }
    
}