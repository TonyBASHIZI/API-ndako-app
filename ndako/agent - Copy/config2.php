<?php
	header("Access-Control-Allow-Origin: *");
	class Constants
	{
		static $DB_HOST = "192.162.69.136";
		static $DB_USER = "c1user";
		static $DB_PASS = "dc2MNReeaVY@";
		static $DB_NAME = "c1db_mangtech";
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
