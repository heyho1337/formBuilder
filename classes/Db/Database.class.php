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
		
		/**
		 * select row or rows from the databse
		*/
		abstract protected function select($table, $columns, $where, $order, $group = null);
    	
		/**
		 * insert rows into a database
		*/
		abstract protected function insert($table);
    	
		/**
		 * update a row's columns in the database
		*/
		abstract protected function update($table, $fields, $where);
		
		/**
		 * create a database table if it is not extist allready
		*/
		abstract protected function create($tableName, $columns);
	}