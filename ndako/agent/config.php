<?php
	header("Access-Control-Allow-Origin: *");
	class Constants
	{
		static $DB_HOST = "localhost";
		static $DB_USER = "Julio";
		static $DB_PASS = "myserver";
		static $DB_NAME = "batiment_db";
		// static $DB_NAME = "c1alohadynam_db";

		public static function connect()
		{
		    $con = new Mysqli(Constants::$DB_HOST,Constants::$DB_USER,Constants::$DB_PASS,Constants::$DB_NAME) or die(Mysqli_errno());
		    if ($con->connect_error) {
		        return null;
		    } else {
		        return $con;
		    }
		    
		}

	}
?>
