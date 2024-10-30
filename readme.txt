=== League of Legends Shortcodes ===
Contributors: TEZZERACT
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CHT9TK45PD3DU
Tags: league_of_legends, api, stats, summoner, league, lol, shortcode
Requires at least: 3.7.0
Tested up to: 3.9.1
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin provides shortcodes related to League of Legends.

== Description ==

This plugin provides shortcodes for League of Legends stats. This plugin is still in work, but the following shortcodes are already done:

* Get Summonername by summonerID and Region. Example:
<b>[summonername region="euw" summonerid="20486792"]</b>

This returns "SoBiT".

* Get League of Summoner by summonerID and Region. Example:
<b>[summonername region="euw" summonerid="20486792" image="true"]</b>

This returns an image of the current League of the Summoner "SoBiT" on EUW. If the image parameter is set "false",
it returns the League as text (for example: "PLATINUM III").

* Get League Points by summonerID and Region. Example:
<b>[points region="euw" summonerid="20486792"]</b>

This returns the amount of current League Points of the summoner "SoBiT" on EUW.

* Get a score (like ELO in the past). Example:
<b>[score region="euw" summonerid="20486792"]</b>

This returns the score (ELO) of summoner "SoBiT" on EUW.


All images used in this plugin are property of Riot Games, Inc.

Riot Games, League of Legends and PvP.net are trademarks, 
services marks, or registered trademarks of Riot Games, Inc.

== Installation ==

1. Download the .zip-file
2. Go to the backend of your wordpress-installation
3. Go to "Plugins" -> "Add new" -> "Upload" and choose the .zip-file
4. Activate the plugin
5. Set your API Key in Settings -> LoL Shortcodes
6. Insert shortcodes in your articles, pages and wherever you want!
7. Have fun, and happy summoning!

== Frequently Asked Questions ==

= Will this plugin be extended? =

Yes, this plugin is very new and the developer is also very new to wordpress plugins.
You can expect many extensions and improvements!

== Screenshots ==

Comming soon

== Changelog ==

= 1.0.1 =
Riot API has updated. This is a fix.

= 1.0 =
Released version using the Riot Games API

= 0.9 =
* Initial version