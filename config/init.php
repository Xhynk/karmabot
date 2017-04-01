<?php
	/**
	 * Karmabot->Init
	 *
	 * @author	Alexander Demchak (Xhynk)
	 * @link	http://jamsandjelli.es/api/slack-api-v2/karmabot/
	 * @link	github.com/xhynk/karmabot/
	 * @package	Karmabot->Init
	 *
	 * @internal { This file Initializes the database connection via mysqli. }
	*/

	##  (\ /)
	## ( . .) ♥ ~< Code Block - i: Require Database Config >
	## c(”)(”)

	require_once( __DIR__ . '/config.php' );

	##  (\ /)
	## ( . .) ♥ ~< Code Block - A: Create & Initialize Karmabot Database Class >
	## c(”)(”)

	class KarmabotDatabase {
		function __construct() {
			$this->connect = mysqli_connect(
				DB_HOST,
				DB_USER,
				DB_PASS,
				DB_NAME
			);

			// DB Connection failed, echo why
			if( !$this->connect ){
				echo "Error: Unable to connect to MySQL." . PHP_EOL;
				echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
				echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
				exit;
			}
		}
	}

	function init_mysqli(){
		global $mysqli;

		$database = new KarmabotDatabase();
		$mysqli = $database->connect;
	}

	init_mysqli();
?>
