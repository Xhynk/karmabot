# Karmabot
Karmabot is a Slack [`Slash Command`](https://api.slack.com/slash-commands) (soon to be @bot) to divvy out "**Ҝᴀʀᴍᴀ**" credits to users.

# About
Karmabot has an array of responses while performing commands, ranging from adding and removing **Ҝᴀʀᴍᴀ** from a user, to even adding a new user to the database. While performing **Ҝᴀʀᴍᴀ** based tasks, such as resetting or adding **Ҝᴀʀᴍᴀ** to a user's balance, there is a small chance of a "rare" or "legendary" response. Currently the response options are an array, and the array has a random item selected from it.

Standard **Ҝᴀʀᴍᴀ** balance is denoted in quotes when displayed, with the Gemstone unicode icon like so:

>Let me check for you:
>@alex has a **Ҝᴀʀᴍᴀ** balance of `💎1000`"

# Commands
>Note: `/karmabot` and `/karmabot,` both work currently.

- `/karmabot -help` - Display some quick tips.
- `/karmabot, give @alex +10 karma` - Add **Ҝᴀʀᴍᴀ** to a user's balance.
- `/karmabot, take -10 karma from @alex` - Subtract **Ҝᴀʀᴍᴀ** from a user's balance.
- `/karmabot @alex -sudo --reset` - Reset a user's **Ҝᴀʀᴍᴀ** back to 0.
- `/karmabot add new player @name "m/f"`. - Add users to the game! "m" or "f" is required for gender pronoun selection.
- `/karmabot, how much karma does @alex have?` - If a **Ҝᴀʀᴍᴀ** function isn't ran, it will run a **Ҝᴀʀᴍᴀ** balance query instead.

# Coming Soon
- I'd like to have a daily or weekly cap, whether it limits users giving, or users receiving. Like a max of me giving any combo of people 100 **Ҝᴀʀᴍᴀ** in a day, or have a max of being able to receive 100.
- I'd like a lottery system, perhaps making use of Action Buttons.
- I'd like "admin only" options, like "reset", which would notify me (or an admin that's defined in the db)
- I'd like some better "human-like" logic.
- I need to not always run math, but maybe that's not realistic.
