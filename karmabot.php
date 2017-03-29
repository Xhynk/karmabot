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

	 /* Example Class:

		 class Order {
			private $my_total;
			private $my_lineitems;

			public function getItems() { return $this->my_lineitems; }
			public function addItem(Product $p) { $this->my_lineitems[] = $p; }
			public function getTotal() { return $this->my_total; }

			public function forJSON() {
			    $items_json = array();
			    foreach($this->my_lineitems as &$item) $items_json[] = $item->forJSON();
			    return array(
			        'total' => $this->getTotal(),
			        'items' => $items_json
			    );
			}
		}

		$o = new Order();
		// do some stuff with it
		$json = json_encode($o->forJSON());

	*/

	// Users will be initiated here:
	$user = new stdClass();
	$user->karma->current;
	$user->name = parse_user();

	# NOTE:	I believe I want to start using the $user object more,
	#		so I'm going to be adding karma balance and stuff to it
	#		to better keep track of it.

	// I almost always want to know the users current Karma balance
	$user->karma->current = $mysqli->query( "SELECT `karma_received` FROM `karmabot_list` WHERE `users`='". $user->name ."'" )->fetch_object()->karma_received;

	// Modify Karma if any is added or removed
	$karma	= intval( $current_karma );
	$karma	= intval( $karma ) + intval( parse_karma_to_add() ); // Attempt to add some karma
	$karma	= intval( $karma ) - intval( parse_karma_to_subtract() ); // Attempt to subtract some karma

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
			'text' => current( explode( '|', compile_initial_response( $response_type, $user->name, $karma ) ) )
		);
	}

	echo substr( json_encode( $response_array ), 1, -1 );
	//var_dump( $karma ); var_dump( $current_karma );

	mysqli_close( $database->connect );
?>
