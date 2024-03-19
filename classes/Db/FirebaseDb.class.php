<?php
    
    namespace Db;
	
	/**
	 * Class Db\FirebaseDb
	*/
	class FirebaseDb extends Database{

		function __construct(){

		}

		private static $instance;

		public static function getInstance(...$args) {
			if (!self::$instance) {
				self::$instance = new self(...$args);
			}
			return self::$instance;
		}

		private function __clone() {}
    	public function __wakeup() {}

		public function select($table, $columns, $where, $order, $group = null){

		}

		public function insert($table){

		}

		public function update($table, $fields, $where){

		}
		

		public function create($tableName, $columns){

		}
	}