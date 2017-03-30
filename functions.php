<?php
	/**
	 * Karmabot->Functions
	 *
	 * @author	Alexander Demchak (Xhynk)
	 * @link	http://jamsandjelli.es/api/slack-api-v2/karmabot/
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
	 * Fetch Karma Functions
	 *
	 * @since 1.0
	 * @return Karma balance for parsed user
	 */
	function fetch_karma( $username ){
		global $mysqli;
		return $mysqli->query( "SELECT `karma_received` FROM `karmabot_list` WHERE `users`='". $username ."'" )->fetch_object()->karma_received;
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
			$this->karma['current'] = fetch_karma( $this->name );
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

		$user->karma['adjusted'] = $karma;
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

	##  (\ /)
	## ( . .) ♥ ~< Code Block - C: Response Functions >
	## c(”)(”)

	/**
	 * Create "Compile Initial Response" Function
	 *
	 * @since	0.1
	 * @return A string with an opening line, and the desired response line with
	 *	Karma balance if that's part of the response type.
	 */
	function compile_initial_response( $response_type, $user, $karma ){
		$max		= 25; // A "1 / $max" chance to do the "I'm sorry Dave" line
		$override	= mt_rand(0, $max);

		if( parse_for_karma_reset() == true ){
			$opener		= ( $override == $max ) ? 'I\'m sorry Dave, I can\'t do that... Haha just kidding, you know I have to! |Hold on, let me try real hard, Master... *HRRRNNGGRHHH!!* _*Bleep! *Bloop!_ Wait, it worked? Yes! ' : 'Well... I guess... |Are you sure? Alright... |Really? Harsh. |How do you sleep at night? |RIP Ҝᴀʀᴍᴀ |Booooo! |Geez, you monster. |Awwww, okay... '; // If $max is picked, do the "Dave" line

			$opening_string = explode( '|', $opener );
			shuffle( $opening_string );
			return reset( $opening_string ) . "$user now has Ҝᴀʀᴍᴀ balance of `💎0`, ouch!";
		} else if( $response_type == 'update' ){
			$opener		= ( $override == $max ) ? 'I\'m sorry Dave, I can\'t do that... Haha just kidding, you know I have to! |Hold on, let me try real hard, Master... *HRRRNNGGRHHH!!* Wait, it worked? Yes! ' : 'Roger that. |Sure thing! |I\'ll get right on that. |At once. |Sure, I\'m not busy! |Of course, allow me: |Okay - |If you insist. |As you wish! |It shall be done. |At once, my liege... |Right away! |Just one moment. |Processing... |Hold on a sec: |Give me just a minute. |Never! Haha just kidding: |Sure! '; // If $max is picked, do the "Dave" line

			$opening_string = explode( '|', $opener );
			shuffle( $opening_string );
			return reset( $opening_string ) . "$user now has `💎$karma`";
		} else if( $response_type == 'fetch' ){
			$opener		= ( $override == $max ) ? 'I\'m sorry Dave, I can\'t check that... Haha just kidding, you know I have to! |Hold on, let me search my database _*real*_ hard, Master... *HRRRNNGGRHHH!!* Wait, it worked? Yes! ' : 'Just a moment... |Hmmm, |Let\'s see... |Let\'s find out, shall we? |Let me check. |Just a sec! |Hold on, |Let me look for you; |Processing... |Hmmmm, |`Unauthorized Database Access!` Haha, jk: |It looks like '; // If $max is picked, do the "Dave" line

			$opening_string = explode( '|', $opener );
			shuffle( $opening_string );
			return reset( $opening_string ) . "$user currently has a Ҝᴀʀᴍᴀ balance of `💎$karma`";
		}
	}
?>
