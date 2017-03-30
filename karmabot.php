<?php
	/**
	 * Karmabot ★
	 *
	 * @author	Alexander Demchak (Xhynk)
	 * @link	http://jamsandjelli.es/api/slack-api-v2/karmabot/
	 * @version	1.1
	 *
	 * To activate Karmabot, use of the following command:
	 *
	 * @example	/karmabot
	 * @example	/karmabot,
	 *
	 * @method	/karmabot, give @alex +50 Karma for not being a turd today!
	 *
	 * @return an array that Slack reads. $_POST vars include ['user_name'] and
	 *	['text'], where text is the part after the /karmabot
	 *
	 * @internal { Karmabot will manage Karma totals in a database, and attempt
	 *	to draw from that database when called, and + or - Karma totals in a
	 *	cutesy, quasi-randomised way. }
	*/

	##  (\ /)
	## ( . .) ♥ ~< Code Block - i: Error Reporting >
	## c(”)(”)

	//error_reporting(E_ALL);
	//ini_set('display_errors', '1');

	##  (\ /)
	## ( . .) ♥ ~< Code Block - A: Karmabot Setup >
	## c(”)(”)

	/**
	 * Modify Header Output Type
	 *
	 * @since	0.1
	 * @internal { Karmabot (Slack, actually) requires JSON payloads.
	 */
	header( "content-type:application/json" );

	/**
	 * Initiate DB Config, MySQLI connection, and Functions
	 *
	 * @since	0.1
	 */
	require_once( __DIR__ . '/init.php' );
	require_once( __DIR__ . '/functions.php' );

	##  (\ /)
	## ( . .) ♥ ~< Code Block - A: Karmabot Setup >
	## c(”)(”)

	/**
	 * Initiate the User Object
	 *
	 * @since	1.1
	 * @internal { The $user was getting complex, going to add some class
 	 *	validation and other things, this will make the user and their Karma
 	 *	easier to work with as Karmabot grows. }
	 */
	$user = new KarmabotUser();

	adjust_karma( $user );

	if( $karma != $current_karma ){
		// Karma has been updated
		$response_type = 'update';
		$mysqli->query( "UPDATE `karmabot_list` SET `karma_received`='". $karma ."' WHERE `users`='". $user->name ."'" );
	} else if( parse_for_karma_reset() == true ){
		//Karma is being reset to 0
		$response_type = 'update';
		$mysqli->query( "UPDATE `karmabot_list` SET `karma_received`='0' WHERE `users`='". $user->name ."'" );
	} else {
		$response_type = 'fetch';
	}

	if( parse_for_help() == true ){
		$response_array[] = array(
			'response_type' => 'ephemeral',
			'text' => "I need a `@name`\r\nI can adjust karma like `give @name +5 karma`\r\nBork something? I can reset with `@name -sudo --reset`"
		);
	} else {
		$response_array[] = array(
			'response_type' => 'in_channel',
			'text' => current( explode( '|', compile_initial_response( $GLOBALS['karma_mod'], $user->name, $user->karma['adjusted'] ) ) )
		);
	}

	//echo substr( json_encode( $response_array ), 1, -1 );

	var_dump( $user );

	mysqli_close( $mysqli );
?>
