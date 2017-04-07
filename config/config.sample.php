<?php
	/**
	 * Karmabot->Config
	 *
	 * @author	Alexander Demchak (Xhynk)
	 * @link	https://jamsandjelli.es/api/slack-api-v2/karmabot/
	 * @link	https://github.com/xhynk/karmabot/
	 * @package	Karmabot->Config
	 *
	 * @internal { This file Initializes the database constants for mysqli. }
	*/

	##  (\ /)
	## ( . .) ♥ ~< Code Block - i: Define Database Constants >
	## c(”)(”)

	define( 'DB_PREFIX', 'databaseprefix_' );
	define( 'DB_NAME', DB_PREFIX . 'karmabot' );
	define( 'DB_USER', DB_PREFIX . 'kbusr' );
	define( 'DB_PASS', 'SuperSecurePassw0rd123!' );
	define( 'DB_HOST', 'localhost' );
	define( 'SCRIPT', '//jamsandjelli.es' . $_SERVER['REQUEST_URI'] );
?>
