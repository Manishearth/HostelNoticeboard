<?php
class MySQL
{	
	private $dbHost='localhost';
	private $dbUsername='root';
	private $dbPassword='toor';
	private $dbName='NoticeBoard';
	private $dbLink;
	
	function MySQL() {$this->connect();}																//Constructor
	function __destruct() {$this->close();}																//Destructor

	/*********************************
	 * Method to connect to database *
	 *********************************/
	private function connect() {	
		$this->dbLink = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
		if (mysqli_connect_error()) {
		user_error("MySQL Error: ".mysqli_connect_errno() . '. ' . mysqli_connect_error());
		die();
		}
	}
	
	/******************************
	 * Method to close connection *
	 ******************************/
	function close()
	{
		@$this->dbLink->close();
	}
	
	/*****************************
	 * Method to run SQL queries *
	 *****************************/
	function  query($sql)
	{	
		$result = $this->dbLink->query($sql);
		if (mysqli_error($this->dbLink)){
			user_error("MySQL Error: ".mysqli_errno($this->dbLink) . '. ' . mysqli_error($this->dbLink));
			die();
		}
		return $result;
	}

	/*********************************************
	 * Custom methods for administrative backend *
	 *********************************************/
	private $_result_a;
	function loadPiTable() {
		$this->_result_a=$this->query("SELECT * from PI");
	}
	function getNextPi() {
		return $this->_result_a->fetch_Object();
	}	

	/****************************************
	 * Custom methods for execution backend *
	 ****************************************/
	private $_result_e;
	function loadQueue($_PiID) {
		$this->_result_e=$this->query("SELECT * from queue where PiID=".$_PiID);
	}
	function getNextDirective() {
		return $this->_result_e->fetch_Object();
	}
	function directiveSuccess($_object) {
		$this->query("DELETE FROM queue where Date = '".$_object->Date."'");
		if (mysqli_error($this->dbLink)){
			user_error("MySQL Error: ".mysqli_errno($this->dbLink) . ': ' . mysqli_error($this->dbLink));
			return false;
		}
		return true;
	}


}






















