<?php
	function get_string_between($string, $start, $end){
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}

	function parse_user(){
		return '@' . get_string_between( $_POST['text'].' x', '@', ' '); // @TODO: Make this allow for strings like "@alex?", "@alex!", or just nothing ends, from queries like "/karmabot give +50 karma to @alex"
	}

	function parse_karma_to_add(){
		return get_string_between( $_POST['text'], ' +', ' karma'); // @TODO: This is called every time, make it so it's not like it be it do.
	}

	function parse_karma_to_subtract(){
		return get_string_between( $_POST['text'], ' -', ' karma'); // @TODO: This is called every time, make it so it's not like it be it do.
	}

	function parse_for_karma_reset(){
		if( strpos( $_POST['text'], '--reset' ) !== false && strpos( $_POST['text'], '-sudo' ) !== false ){
			return true;
		}
	}

	function parse_for_help(){
		if( strpos( $_POST['text'], '-help' ) !== false ){
			return true;
		}
	}

	function compile_initial_response( $response_type, $user, $karma ){
		$max		= 25; // A 1/$max chance to do the "I'm sorry Dave" line
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
