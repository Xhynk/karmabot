<?php
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
?>
