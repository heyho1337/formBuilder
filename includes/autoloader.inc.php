<?php
	spl_autoload_register('loadClasses');

	function loadClasses($className){
		$path = "classes/";
		$extension = ".class.php";
		$fullPath = str_replace("\\","/",$path.$className.$extension);
		if(!file_exists($fullPath)){
			return false;
		}
		include $fullPath;
	}