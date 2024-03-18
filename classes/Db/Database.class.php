<?php 

	namespace Db;
	
	/**
	 * Abstract Class Db\Database
	 */
	abstract class Database{

		protected $conn;
		protected const dbUser = "";
		protected const dbPass = "";
		protected const dbName = "";
		protected const tbl_prefix = "";

		private static $instance;

		public static function getInstance(...$args) {
			if (!self::$instance) {
				self::$instance = new self(...$args);
			}
			return self::$instance;
		}

		private function __clone() {}
    	public function __wakeup() {}
		
		/**
		 * select row or rows from the databse
		*/
		abstract public function select($table, $columns, $where, $order, $group = null);
    	
		/**
		 * insert rows into a database
		*/
		abstract public function insert($table);
    	
		/**
		 * update a row's columns in the database
		*/
		abstract public function update($table, $fields, $where);
		
		/**
		 * create a database table if it is not extist allready
		*/
		abstract public function create($tableName, $columns);
	}