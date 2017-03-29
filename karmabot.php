<?php
	#error_reporting(E_ALL);
	#ini_set('display_errors', '1');

	/**
	 * Karmabot ★
	 *
	 * @author	Alexander Demchak (Xhynk)
	 * @link	http://jamsandjelli.es/api/slack-api-v2/karmabot/
	 * @version	0.1
	 *
	 * To activate Karmabot, use of the following command:
	 *
	 * @example	/karmabot
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

	// Output needs to be JSON
	header( "content-type:application/json" );

	// Include our MySQLi configuration
	require_once( __DIR__ . '/init.php' );
	require_once( __DIR__ . '/functions.php' );
	$mysqli	= $database->connect;

	$user = strtolower( parse_user() );

	$current_karma	= $mysqli->query( "SELECT `karma_received` FROM `karmabot_list` WHERE `users`='". $user ."'" )->fetch_object()->karma_received;

	// Modify Karma if any is added or removed
	$karma	= intval( $current_karma );
	$karma	= intval( $karma ) + intval( parse_karma_to_add() ); // Attempt to add some karma
	$karma	= intval( $karma ) - intval( parse_karma_to_subtract() ); // Attempt to subtract some karma

	if( $karma != $current_karma ){
		// Karma has been updated
		$response_type = 'update';
		$mysqli->query( "UPDATE `karmabot_list` SET `karma_received`='". $karma ."' WHERE `users`='". $user ."'" );
	} else if( parse_for_karma_reset() == true ){
		//Karma is being reset to 0
		$response_type = 'update';
		$mysqli->query( "UPDATE `karmabot_list` SET `karma_received`='0' WHERE `users`='". $user ."'" );
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
			'text' => current( explode( '|', compile_initial_response( $response_type, $user, $karma ) ) )
		);
	}

	echo substr( json_encode( $response_array ), 1, -1 );
	//var_dump( $karma ); var_dump( $current_karma );

	mysqli_close( $database->connect );
?>
