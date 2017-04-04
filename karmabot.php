<?php
	/**
	 * Karmabot ★
	 *
	 * @author	Alexander Demchak (Xhynk)
	 * @link	http://jamsandjelli.es/api/slack-api-v2/karmabot/
	 * @link	github.com/xhynk/karmabot/
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
	require_once( __DIR__ . '/config/init.php' );
	require_once( __DIR__ . '/lib/functions.php' );

	##  (\ /)
	## ( . .) ♥ ~< Code Block - A: Karmabot Setup >
	## c(”)(”)

	/**
	 * Initiate the User Object, Modify Karma
	 *
	 * @since	1.1
	 * @internal { The $user was getting complex, going to add some class
 	 *	validation and other things, this will make the user and their Karma
 	 *	easier to work with as Karmabot grows. }
	 */
	$user = new KarmabotUser();
	adjust_karma( $user );

	$invoking_user = $_POST['user_name'];

	if( parse_for_help() == true ){
		$response_type = 'ephemeral';
		$response_text = ">Invoke me with the `/karmabot` command\n>I can give users Ҝᴀʀᴍᴀ - `/karmabot, give @$invoking_user +10 karma`\n>I giveth, I taketh away - `/karmabot, take -10 karma from @$invoking_user`\n>Bork something? I can reset Ҝᴀʀᴍᴀ with `/karmabot @$invoking_user -sudo --reset`\n>Check a users Ҝᴀʀᴍᴀ balance with something like `/karmabot, how much karma does @$invoking_user have?`\n>Add Users with the command `/karmabot add new player @name \"m/f\"`.\nRead more on GitHub: https://github.com/Xhynk/karmabot";
	} else if( parse_for_joke() == true ){
		$joke = json_decode( file_get_contents( 'https://api.chucknorris.io/jokes/random' ) );

		$response_type = 'in_channel';
		$response_text = $joke->value;
	} else {
		$response_type = 'in_channel';
		$response_text = compile_response( $user );
	}

	/**
	 * Build the Slack Reponse JSON Array and Output It
	 *
	 * @since	1.1
	 */
	$response_array = array(
		'response_type' => $response_type,
		'text' => $response_text,
	);
	echo json_encode( $response_array );

	/**
	 * Close the MySQLI Connection
	 *
	 * @since	0.1
	 */
	mysqli_close( $mysqli );
?>
