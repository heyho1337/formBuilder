<?php
    
    namespace Db;
	
	/**
	 * Class Db\SqlDb
	*/
	class SqlDb extends Database{

		protected const dbHost = "";
		
		/***
		 * database table for the module's texts  
		*/
		protected array $formTable = [
			'form_id' => 'INT AUTO_INCREMENT PRIMARY KEY',
			'form_fields' => 'TEXT'
		];

        function __construct(){
           	$dsn = 'mysql:host='.self::dbHost.';dbname='.self::dbName.';port=3311;charset=utf8';
            try {
              	$this->conn = new \PDO($dsn, self::dbUser, self::dbPass, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));
            } catch (\PDOException $e) {
                echo $e->getMessage();
                $response["status"] = "error";
                $response["message"] = 'Connection failed: ' . $e->getMessage();
                $response["data"] = null;
                exit;
            }
        }
        
		/**
		 * select row or rows from the databse
		 * @param string $table - the database table's name
		 * @param array $columns - the columns that you want to select from the database: 
		 * array('column1','column2','column3') for all columns: array('*')
		 * @param array $where - the conditions for selecting rows from the database:
		 * array('column1' => $value1, 'column2' => $value2)
		 * @param string $order - the ordering of the rows
		 * @param string $group - you can use groupby here
		 * @return array ['status', 'message', 'data', 'rowCount', 'query'];
		*/
        protected function select($table, $columns, $where, $order,$group = null){
            try{
                $a = array();
                $w = "";
                $cols = "";
                $where_string = "where 1=1 ";
                if(is_array($where)){
                    foreach ($where as $key => $value) {
                        $w .= " and " .$key. " like :".$key;
                        $a[":".$key] = $value;
                    }
					$where_string.= $w;
                }
				$countCol = count($columns);
                for($i = 0;$i<$countCol;$i++){
					$cols .= $columns[$i];
					if ($i < $countCol - 1) {
						$cols .= ",";
					}
                }

				$stmt = $this->conn->prepare("select ".$cols." from ".self::tbl_prefix.$table." ".$where_string." ".$group." ".$order);
                $stmt->execute($a);
                $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);

				$count = $this->conn->prepare("select ".$cols." from ".self::tbl_prefix.$table." ".$where_string." ".$group." ".$order);
                $count->execute($a);

                if(isset($rows)){
					$response["status"] = "success";
                    $response["message"] = "Data selected from database";
                }else{
                    $response["status"] = "warning";
                    $response["message"] = "No data found.";
                }
                $response["data"] = $rows;
				$response['rowCount'] = $count->rowCount();
            }catch(\PDOException $e){
                $response["status"] = "error";
                $response["message"] = 'Select Failed: ' .$e->getMessage();
                $response["data"] = null;
            }
            $response['query'] = "select ".$cols." from ".self::tbl_prefix.$table." where 1=1 ". $w." ".$group." ".$order;
            return $response; 
        }
		
		/**
		 * insert rows into a database table based on the $_POST array.
		 * Before you run this method you need to set the $_POST with the
		 * columns and their values:
		 * $_POST['column1'] = $value1;
		 * $_POST['column2'] = $value2;
		 * @param string $table - database table's name
		 * @return string - the inserted row's id
		*/
		protected function insert($table){
			$data = $_POST;
			$type = $this->conn->query("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='".self::tbl_prefix.$table."'");
			$type2 = $type->fetchAll(\PDO::FETCH_COLUMN);
			array_shift($type2);
			$q = $this->conn->prepare("DESCRIBE ".self::tbl_prefix.$table);
			$q->execute();
			$getFields = $q->fetchAll(\PDO::FETCH_COLUMN);
		
			array_shift($getFields);
			$implodedFields = implode(", :", $getFields);
			$sql = "INSERT INTO ".self::tbl_prefix.$table." (".implode(", ", $getFields).") VALUES(:".$implodedFields.")";
			$insert = $this->conn->prepare($sql);
			$none		= "";

			foreach ($getFields as $dbKey => $dbValue) {
				if(isset($data[$dbValue])) {
					$insert->bindValue(":$dbValue", $data[$dbValue]);
				}
				else {
					if($type2[$dbKey] === "int"){
						$insert->bindValue(":$dbValue", 0);
					}
					else{
						$insert->bindValue(":$dbValue", $none);
					}
				}
			}
		
			$insert->execute();
			$last_insert_id	= $this->conn->lastInsertId();
			return $last_insert_id;
		}

		/**
		 * update a row's columns in the databse
		 * @param string $table - the database table's name
		 * @param array $fields - the columns that you want to set in the database row: 
		 * array('column1' => $value1, 'column2' => $value2)
		 * @param array $where - the conditions for selecting the row from the database:
		 * array('column1' => $value1, 'column2' => $value2)
		 * @return array ['status', 'message', 'data', 'rowCount', 'query'];
		*/
		protected function update($table,$fields,$where){
			$sql="UPDATE ".self::tbl_prefix.$table." SET ";
			$j = 0;
			$w = " WHERE ";
			foreach ($where as $key => $value) {
				if($j === 0){
					$w.=$key." = :".$key;
				}
				else{
					$w.= " and " .$key. " = :".$key;
				}
				$j++;
			}
			$field_count = count($fields);
			$i = 0;
			foreach($fields as $dbKey => $dbValue){
				$i++;
				if($i === $field_count){
					$sql.=$dbKey." = :".$dbKey."";
				}
				else{
					$sql.=$dbKey." = :".$dbKey.",";
				}
			}
			$sql.=$w;
			$stmt = $this->conn->prepare($sql);
			$k = 0;
			foreach($fields as $dbKey => $dbValue){
				$stmt->bindParam(':'.$dbKey, $fields[$dbKey]);
			}
			foreach ($where as $key => $value) {
				$stmt->bindParam(':'.$key, $value);
			}
			$result = $stmt->execute();
			return $result;
		}

		/**
		 * create a database table if it is not exists allready
		 * @param string $tablename - name of the database table
		 * @param array $columns - the columns you want in the databse,example:
		 * [
		 * 'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
		 * 'label' => 'VARCHAR(50)'
		 * }
		 * @return boolean
		*/
		protected function create($tableName, $columns){
			try {
				$query = "CREATE TABLE IF NOT EXISTS " . self::tbl_prefix . $tableName . " (";
				foreach ($columns as $columnName => $columnDefinition) {
					$query .= $columnName . " " . $columnDefinition . ",";
				}
				$query = rtrim($query, ",") . ")";
				$stmt = $this->conn->prepare($query);
				$stmt->execute();
				return true;
			} catch (\PDOException $e) {
				return false;
			}
		}
    }