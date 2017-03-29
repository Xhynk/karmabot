# Karmabot
Karmabot is a Slack [`Slash Command`](https://api.slack.com/slash-commands) (soon to be @bot) to divvy out "**Ҝᴀʀᴍᴀ**" credits to users.

# About
**Ҝᴀʀᴍᴀ** is currently database driven, and users are added manually to the database. As of 3/29/17, the only action commands /karmabot understands are commands to add, remove, and reset **Ҝᴀʀᴍᴀ**. Karmabot has an array of responses while performing the command, and has a few "rare" responses that a bit funnier than the standard ones.

Standard **Ҝᴀʀᴍᴀ** balance is denoted in quotes when displayed, with the Gemstone unicode icon like so:

"Let me check for you: @alex has a **Ҝᴀʀᴍᴀ** balance of `💎1000`"

# Coming Soon
- I'd like to be able to add and remove users as well, directly from Karmabot. Since we're not adding team members yet, it's not a huge deal.
- I'd like to have a daily or weekly cap, whether it limits users giving, or users receiving. Like a max of me giving any combo of people 100 **Ҝᴀʀᴍᴀ** in a day, or have a max of being able to receive 100.
- I'd like a lottery system, perhaps making use of Action Buttons.
- I'd like "admin only" options, like "reset", which would notify me (or an admin that's defined in the db)
- I'd like some better "human-like" logic.
- I need to not always run math, but maybe that's not realistic.
