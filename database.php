<?php
	
namespace Exchange;
use PDO;

	class Database
	{
	
		public static $host = "localhost";
		public static $database = "exchange";
		public static $username = "havok2905";
		public static $password = "ee5ahcqh";
		public static $DBH;
		
		/** Connects to the database using PDO
		* @author Christopher McLean
		* @version 1.0 
		*/
		public static function makeConnection()
		{
			try
			{
				self::$DBH = new PDO("mysql:host=".self::$host.";dbname=".self::$database, self::$username, self::$password);
			}
			catch(PDOException $e)
			{
				echo $e -> getMessage();
			}
		}

		/** Ends connection to the database using PDO
		* @author Christopher McLean
		* @version 1.0 
		*/
		public static function killConnection()
		{
			self::$DBH = null;
		}

		/** Inserts into the database using PDO
		* @author Christopher McLean
		* @version 1.0 
		* @param    $table, $fields, $data
		*/
		public static function insert($table ,$fields, $data)
		{

			//build unnamed placeholders for query
			$countstring = "";
			for($x=0; $x<count($fields); $x++)
			{
				$countstring .= "?, ";
			}

			//hack off extra space and comma from ?, ?, ?,<space>
			$countstring = substr($countstring, 0, -2);

			//make fields string for query
			$fields = implode(", ", $fields);

			//construct query and execute it
			$STH = self::$DBH->prepare("INSERT INTO ".$table." (".$fields.") values (".$countstring.")");
			$STH->execute($data);
		}

		/** Updates into the database using PDO
		* @author Christopher McLean
		* @version 1.0 
		* @param    $table, $fields, $selector, $data
		*/
		public static function update($table, $fields, $selector, $data)
		{
			//add unnamed placeholder to the end of each field
			foreach ($fields as $key => &$value) 
			{
				$value .= "=?";
			}

			//make fields string for query
			$fields = implode(", ", $fields);

			//construct query and execute it
			$STH = self::$DBH->prepare("UPDATE $table SET $fields WHERE $selector=?");
			$STH->execute($data);
		}
	}