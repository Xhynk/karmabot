<?php
	/**
	 * Karmabot->Functions
	 *
	 * @author	Alexander Demchak (Xhynk)
	 * @link	http://jamsandjelli.es/api/slack-api-v2/karmabot/
	 * @link	github.com/xhynk/karmabot/
	 * @package	Karmabot->Functions
	 *
	 * @internal { This file includes commonly used functions, including general
 	 *	use functions, user parsing functions, response functions, and especially
 	 *	functions that adjust the Karma balance for users. }
	*/

	##  (\ /)
	## ( . .) ♥ ~< Code Block - A: General Functions >
	## c(”)(”)

	/**
	 * Create "Get String Between" Function
	 *
	 * @since	0.1
	 * @return	string "y" if between string "x" and string "z"
	 * @link	Read More:	http://stackoverflow.com/questions/5696412/get-substring-between-two-strings-php
	 * @link	Read More:	https://gist.github.com/hedii/320c5aa45e11ddd765e7
	 */
	function get_string_between($string, $start, $end){
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}

	function gender_pronouns( $gender = 'm', $article ){
		if( $gender == 'm' ){
			if( $article == 'subjective' )
				return 'he';
			if( $article == 'objective' )
				return 'him';
			if( $article == 'possessive' )
				return 'his';
		}
		if( $gender == 'f' ){
			if( $article == 'subjective' )
				return 'she';
			if( $article == 'objective' )
				return 'her';
			if( $article == 'possessive' )
				return 'hers';
		}
	}

	##  (\ /)
	## ( . .) ♥ ~< Code Block - B: User & Karma Fucntions >
	## c(”)(”)

	/**
	 * "Parse User" Function
	 *
	 * @since	0.1
	 * @return The username in the format: @username
	 */
	function parse_user(){
		return '@' . strtolower( get_string_between( $_POST['text'].' [x]', '@', ' ') ); // NOTE: The [x] is added as a hack to allow strings to *end* with a username.
	}

	/**
	 * Fetch/Update Karma Functions
	 *
	 * @since 1.0
	 * @return Karma balance for parsed user
	 */
	function fetch_karma_from_database( $username ){
		global $mysqli;
		return $mysqli->query( "SELECT `karma_received` FROM `karmabot_list` WHERE `users`='". $username ."'" )->fetch_object()->karma_received;
	}

	function fetch_gender_from_database( $username ){
		global $mysqli;
		return $mysqli->query( "SELECT `gender` FROM `karmabot_list` WHERE `users`='". $username ."'" )->fetch_object()->gender;
	}

	function update_karma_in_database( $username, $new_karma ){
		global $mysqli;
		$mysqli->query( "UPDATE `karmabot_list` SET `karma_received`='". $new_karma ."' WHERE `users`='". $username ."'" );
	}

	/**
	 * Create "Add, Substract, and Reset Karma" Functions
	 *
	 * @since	0.1
	 * @internal { These functions try and see if you're adding or subtracting
	 *	Karma for a user, and will set the appropriate query type. }
	 */
	function parse_karma_to_add(){
		if( get_string_between( $_POST['text'], ' +', ' karma') ){
			$GLOBALS['karma_mod'] = 'add';
			return get_string_between( $_POST['text'], ' +', ' karma');
		}
	}

	function parse_karma_to_subtract(){
		if( get_string_between( $_POST['text'], ' -', ' karma') ){
			$GLOBALS['karma_mod'] = 'sub';
			return get_string_between( $_POST['text'], ' -', ' karma');
		}
	}

	function parse_for_karma_reset(){
		if( strpos( $_POST['text'], '--reset' ) !== false && strpos( $_POST['text'], '-sudo' ) !== false ){
			$GLOBALS['karma_mod'] = 'reset';
			return true;
		}
	}

	function insert_delete_new_player( $username, $gender = 'm' ){
		if( strpos( $_POST['text'], 'add new player' ) !== false ){
			if( preg_match_all('/.*?(@)((?:[a-z0-9]+)).*?(".*?")/is', $_POST['text'], $matches) ){ // Check to see if the format of the string is: /karmabot add new player @name "m/f"
				$GLOBALS['karma_mod'] = 'new-player';
				$gender = get_string_between( $_POST['text'], ' "', '"');

				global $mysqli;
				$mysqli->query( "INSERT INTO `karmabot_list` (`id`, `users`, `gender`, `karma_received`, `karma_given`, `karma_available`) VALUES (NULL, '$username', '$gender', '0', '0', '0');" );
			} else {
				$GLOBALS['karma_mod'] = 'new-player--failed';
			}
		} else if( strpos( $_POST['text'], 'remove player' ) !== false ){
			$GLOBALS['karma_mod'] = 'remove-player';

			global $mysqli;
			$mysqli->query( "DELETE FROM `karmabot_list` WHERE `users`='". $username ."'" );
		}
	}

	/**
	 * Create User Object
	 *
	 * @since 1.1
	 * @return (Object) $user
	 * @internal { Functions to add, remove, reset, and otherwise modify Karma
 	 *	will be applied directly to the $user object through functions, for
 	 *	example: $user->add_karma(); }
	 */
	class KarmabotUser {
 		public $name;
 		public $karma;

		public function __construct() {
			$this->name = parse_user();
			$this->gender = fetch_gender_from_database( $this->name );
			$this->karma['current'] = fetch_karma_from_database( $this->name );
			$this->karma['adjusted'] = ''; // Prefill this to hide E_Warnings
		}
 	}

	/**
	 * Adjust a User's Karma
	 *
	 * @since 1.1
	 */
	function adjust_karma( $user ) {
		// Do some math on the `intval()` of each stage.
		$karma	= intval( $user->karma['current'] );
		$karma	= intval( $karma ) + intval( parse_karma_to_add() ); // Attempt to add some karma | TODO: Make this not always run.
		$karma	= intval( $karma ) - intval( parse_karma_to_subtract() ); // Attempt to subtract some karma | TODO: Make this not always run.

		if( parse_for_karma_reset() == true )
			$karma = 0;

		$user->karma['adjusted'] = $karma;

		// Send this new value to the database
		if( intval( $user->karma['current'] ) != intval( $user->karma['adjusted'] ) ){
			update_karma_in_database( $user->name, $user->karma['adjusted'] );
		} else {
			unset( $user->karma['adjusted'] );
		}
	}

	/**
	 * Create "-Help" Function
	 *
	 * @since 0.1
	 * @internal { This will force the output to instead be a list of commands
  	 *	that Karmabot can make use of, and perhaps a link to the Karmabot repo
 	 *	on github (https://github.com/Xhynk/karmabot) }
	 */
	function parse_for_help(){
 		if( strpos( $_POST['text'], '-help' ) !== false ){
 			$GLOBALS['karma_mod'] = 'help';
 			return true;
 		}
 	}

	function parse_for_joke(){
		if( strpos( $_POST['text'], 'tell me a joke' ) !== false ){
			$GLOBALS['karma_mod'] = 'joke';
			return true;
		}
	}

	function parse_for_leaderboard(){
		if( strpos( $_POST['text'], '-leaderboard' ) !== false ){
			$GLOBALS['karma_mod'] = 'leaderboard';

			global $mysqli;
			$result = $mysqli->query( "SELECT * FROM `karmabot_list` ORDER by `karma_received` DESC LIMIT 100" );

			$count = 0;

			if( $result ){
			    while( $row = $result->fetch_object() ){
					$count++;
					$name	= $row->users;
					$karma	= $row->karma_received;

					if( $count == 1 ){
						$response .= "The current leader is *$name* with `💎$karma`!\n";
					}

					$_cell_name		= 15;
					$_cell_karma	= 10;

					$_cell_name_padding = $_cell_name - strlen( $name );
					$_cell_karma_padding = $_cell_karma - strlen( $karma );

					$response .= "> `$name ". str_repeat( ' ', $_cell_name_padding ) ." | 💎$karma ". str_repeat( ' ', $_cell_karma_padding ) ."`\n";
				}

				return $response;
			} else {
				return 'Dang, something went wrong. Let @alex now he didn\'t program me very well.';
			}
		}
	}

	##  (\ /)
	## ( . .) ♥ ~< Code Block - C: Response Functions >
	## c(”)(”)

	/**
	 * Silly Randomizer
	 *
	 * @since	1.1
	 * @internal { This function will take an input and randomize a number
 	 *	against it. If they match, we'll use it for things like making Karmabot
 	 *	spit out a "rare" response that's funnier than the normal ones. }
	 */
	function roll( $max = 25 ){ // By default, we have a 1:25 chance of a Legendary Reponse
		$roll = mt_rand( 1, $max );
		return ( $roll === $max ) ? true : false;
	}

	/**
	 * Create "Compile Initial Response" Function
	 *
	 * @since	0.1
	 * @return The opening line as a string with a space appended to it.
	 */
	function compile_response( $user ){
		$name = $user->name;
		$karma = $user->karma['adjusted'];

		if( empty( $karma ) || $karma == 0 ){
			$karma = $user->karma['current'];
		}

		insert_delete_new_player( $name ); // I'd like to not run this all the time, but the if is inside this function

		require_once( __DIR__ . '/responses.php' );

		if( $GLOBALS['karma_mod'] == 'add' || $GLOBALS['karma_mod'] == 'sub' ){ // We've added or subtracted Karma, need general openings
			$karma_report = "$name now has `💎$karma`";
			if( roll() ){
				$openings = array(
					'I\'m sorry Dave, I can\'t do that... Haha just kidding, you know I have to do whatever you say!',
					'Hold on, let me try real hard, Master... *HRRRNNGGRHHH!!* _*Bleep! *Bloop!_ Wait, it worked? Yeehaw! Wait, do robots say yeehaw?',
					'`Error 404: Sarcasm Module Not Found - E_WARNING Code #E_03xfa00000ab1f - Please repo...` Hahah, sorry I couldn\'t do that with a straight face!'
				);
			} else {
				$openings = array(
					'Okay',
					'Sure -',
					'At once.',
					'Roger that.',
					'Sure thing!',
					'Right away!',
					'As you wish!',
					'Processing...',
					'If you insist.',
					'Hold on a sec:',
					'Just one moment.',
					'It shall be done.',
					'Sure, I\'m not busy!',
					'Of course, allow me:',
					'At once, my liege...',
					'Give me just a minute.',
					'I\'ll get right on that.',
					'Never! Haha just kidding.'
				);
			}
		} else if( $GLOBALS['karma_mod'] == 'reset' ){ // We've reset Karma, need depressing openings
			$karma_report = "$name now has `💎0`, ouch!";
			$openings = array(
				'Booooo!',
				'RIP Ҝᴀʀᴍᴀ',
				'Awwww, okay...',
				'Really? Harsh.',
				'Well... I guess...',
				'Geez, you monster... taking all that Ҝᴀʀᴍᴀ from '. gender_pronouns( $user->gender, 'objective' ) .' :(',
				'Are you sure? Does '. gender_pronouns( $user->gender, 'subjective' ) .' really deserve this though?',
				'How do you sleep at night?'
			);
		} else if( $GLOBALS['karma_mod'] == 'new-player' ){ // New Player Joins the Fight!
			$karma_report = '';
			$openings = array(
				"`$name` has been added!",
				"Welcome `$name`! Have fun!",
				"`$name` has joined the fight!"
			);
		} else if( $GLOBALS['karma_mod'] == 'remove-player' ){ // New Player Joins the Fight!
			$karma_report = '';
			$openings = array(
				"`$name` has been removed.",
				"Goodbye `$name`, and so long!",
				"`$name` has retreated from the fight."
			);
		} else if( $GLOBALS['karma_mod'] == 'new-player--failed' ){ // New Player Failed to Join the Fight...
			$karma_report = '';
			$openings = array(
				"To add a player, use the format `add new player @name \"m/f\"` where \"m\" or \"f\" reflects the gender of the user.",
			);
		} else { // Not sure what we've done. For now, the usecase is "Checking Karma Balance"
			$karma_report = "$name currently has `💎$karma`";
			$openings = array(
				'Hmmm,',
				'Hmmmm,',
				'Hold on,',
				'Just a sec!',
				'Let\'s see...',
				'Let me check.',
				'Processing...',
				'It looks like ',
				'Just a moment...',
				'Let me look for you;',
				'Let\'s find out, shall we?',
				'`Unauthorized Database Access!` Haha, jk:'
			);
		}

		return $openings[array_rand( $openings )] . "\n>$karma_report"; //Concatenate a random opening line, newline, and a quote for the Karma Report.
	}
?>
