<?php

namespace application\core;

//use application\lib\Db;
use application\lib\SafeMySQL;

abstract class Model {

	public $db;
	
	public function __construct() {
		//$this->db = new Db;

		//Для работы с классом SafeMySQl
		$this->db = new SafeMySQL;
	}

}