<?php

	/**
	 *	PDO Database class
	 * @author Christopher McLean
	 * @version 3.0 
	 *
	 */

	namespace //namespace here;
	use PDO;

	class Database
	{
		public static $host;
		public static $database;
		public static $username;
		public static $password;		
		public static $DBH;
		
		/** Connects to the database using PDO
		* @author Christopher McLean
		* @version 1.0 
		*/
		public static function makeConnection()
		{
			$dbc = DBConfig::get_connection_settings();
			self::$host = $dbc['host'];
			self::$database = $dbc['database'];
			self::$username = $dbc['username'];
			self::$password = $dbc['password'];

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

		/** Returns true or false if a piece of data exists in a table
		* @author Christopher McLean
		* @version 1.0 
		* @param    $table, $data
		*/
		public static function doesExist($table, $data)
		{
			try
			{
				$STH = self::$DBH -> prepare("SELECT id FROM $table WHERE id=?");
				$STH -> execute($data);
				$row = $STH -> fetch();
			}
			catch(PDOException $e)
			{
				echo $e->getMessage();
			}
			
			if($row == false)
			{
				return false;
			}
			else
			{
				return true;
			}
		}

		/** Returns array of a select column in a table
		* @author Christopher McLean
		* @version 1.0 
		* @param    $table, $fields
		*/
		public static function getArray($fields, $table)
		{
			$fields = self::condenseFields($fields);

			try
			{
				$STH = self::$DBH -> query("SELECT $fields FROM $table");
				$response = $STH->fetchAll(PDO::FETCH_ASSOC);
			}
			catch(PDOException $e)
			{
				echo $e->getMessage();
			}	

			return $response;
		}

		/** Takes in a query in the form of a string and 
		*	executes either a prepare or query method.
		* @author Christopher McLean
		* @version 1.0 
		* @param    $querystring, $data
		*/
		public static function query($querystring, $data=null)
		{
			if($data == null)
			{
				try
				{
					$STH = self::$DBH-> query($querystring);
				}
				catch(PDOException $e)
				{
					echo $e->getMessage();
				}
			}
			else
			{
				try
				{
					$STH = self::$DBH-> prepare($querystring);
					var_dump($STH); 
					$STH->execute($data);
				}
				catch(PDOException $e)
				{
					echo $e->getMessage();
				}
			}

			$response = $STH->fetchAll(PDO::FETCH_OBJ);
			return $response;
		}

		/** Takes in an array of fields and returns an
		* 	imploded string.
		* @author Christopher McLean
		* @version 1.0 
		* @param    $field
		* @return   $string of fields for use in a query
		*/
		public static function condenseFields($fields)
		{
			$fields = implode(", ", $fields);
			return $fields;
		}

		/** Takes in an array of fields and returns an
		* 	imploded string with placeholders added.
		* @author Christopher McLean
		* @version 1.0 
		* @param    $field
		* @return   $string of fields with placeholders for use in a query
		*/
		public static function condenseWithPlaceholders($fields)
		{
			foreach ($fields as $key => &$value) 
			{
				$value .= "=?";
			}
			$fields = implode(", ", $fields);

			return $fields;
		}

		/** Takes in an array of fields and returns a
		string of placeholders
		* @author Christopher McLean
		* @version 1.0 
		* @param    $array
		* @return   $string of placeholders
		*/
		public static function buildPlaceholders($array)
		{
			$placeholders = "";
			
			foreach ($array as $key => $value)
			{
				$placeholders .= "?, ";
			}

			$placeholders = substr($placeholders, 0, -2);
			
			return $placeholders;
		}
	}
?>