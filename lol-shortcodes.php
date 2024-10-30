<?php
/*
Plugin Name: League of Legends Shortcodes
Plugin URI: http://tezzeract.com/lol-shortcodes
Description: This plugin provides shortcodes related to League of Legends
Version: 1.0.1
Author: Klemens Forster
Author URI: http://tezzeract.com
License: GPL2


Copyright 2014 Klemens Forster (email : klemens.forster@hotmail.de)

All images included in this plugin are property of Riot Games, Inc.

Riot Games, League of Legends and PvP.net are trademarks, 
services marks, or registered trademarks of Riot Games, Inc.


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$options_temp = get_option('lolsc_options');
$myKey = $options_temp['apikey'];

function summoner_name($atts) {
   extract(shortcode_atts(array(
      'summonerid' => '20486792',
      'region' => 'euw',
   ), $atts));
$data = get_all_stats($summonerid, $region);
return $data->summonername;
}

function summoner_points($atts) {
   	extract(shortcode_atts(array(
      		'summonerid' => '20486792',
      		'region' => 'euw',
   	), $atts));
$data = get_all_stats($summonerid, $region);
return $data->points;
}

function league($atts) {
   	extract(shortcode_atts(array(
      		'summonerid' => '20486792',
      		'region' => 'euw',
      		'image' => 'false',
		'size' => '192',
   	), $atts));
        $data = get_all_stats($summonerid, $region);
        $return = '';
	if($image == 'true'){
   		$return = '<img src="' . plugin_dir_url(__FILE__) . 'images/ranks/' . str_replace(' ', '_', $data->league) . '.png" width="' . $size . '" height="' . $size . '" alt="' . $data->league . '" style="vertical-align:middle"/>';
	}else{
   		$return = $data->league;
	}
	return $return;
}

function lol_score($atts) {
   	extract(shortcode_atts(array(
      		'summonerid' => '20486792',
      		'region' => 'euw',
   	), $atts));
        $data = get_all_stats($summonerid, $region);
        $league = $data->league;
	$leaguePoints = $data->points;
	$league_arr = array("UNRANKED", "BRONZE", "SILVER", "GOLD", "PLATINUM", "DIAMOND", "IV", "III", "II", "I", "V");
	$points_arr = array("0", "800", "1150", "1500", "1850", "2200", "70", "140", "210", "280", "0");
	$league = str_replace($league_arr, $points_arr, $league);
	$score = array_sum(explode(" ", $league));
	$score += $leaguePoints * 0.7;
	$score = round($score);
	return $score;
}

function get_all_stats($summonerid, $region){
    global $wpdb;
    global $myKey;
    if(strtolower($region) == "euw"){
	$endpoint = "euw";
    }else{
	$endpoint = "prod";
    }
    $data = $wpdb->get_row("SELECT * FROM wp_lolshortcodes WHERE summonerid LIKE '$summonerid' AND region LIKE '$region'");
    if(empty($data)){
	$wpdb->query("INSERT INTO wp_lolshortcodes(summonerid,region) VALUES('$summonerid','$region')");
	$json = @file_get_contents('https://' . $endpoint . '.api.pvp.net/api/lol/' . $region . '/v2.4/league/by-summoner/' . $summonerid . '/entry?api_key=' . $myKey);
	if($json != false){
		$obj = json_decode($json);
       		$summonername = '';
		$league = '';
       		$points = '';
		$hasLeague = false;
		foreach($obj->$summonerid as $queue){
				if($queue->queue == 'RANKED_SOLO_5x5'){
       	                		$summonername = $queue->entries[0]->playerOrTeamName;
					$league = $queue->tier . ' ' . $queue->entries[0]->division;
       	                		$points = $queue->entries[0]->leaguePoints;
					$hasLeague = true;
					break;
				}
		}
		if($hasLeague){
			$wpdb->query("UPDATE wp_lolshortcodes SET summonername='$summonername', league='$league', points='$points' WHERE summonerid LIKE '$summonerid' AND region LIKE '$region'");
		}else{
			$json = @file_get_contents('https://' . $endpoint . '.api.pvp.net/api/lol/' . $region . '/v1.4/summoner/' . $summonerid . '?api_key=' . $myKey);
			$obj = json_decode($json);
			$summonername = $obj->$summonerid->name;
			$wpdb->query("UPDATE wp_lolshortcodes SET summonername='$summonername', league='UNRANKED', points='0' WHERE summonerid LIKE '$summonerid' AND region LIKE '$region'");
		}
	}else{
		$json = @file_get_contents('https://' . $endpoint . '.api.pvp.net/api/lol/' . $region . '/v1.4/summoner/' . $summonerid . '?api_key=' . $myKey);
		$obj = json_decode($json);
		$summonername = $obj->$summonerid->name;
		$wpdb->query("UPDATE wp_lolshortcodes SET summonername='$summonername', league='UNRANKED', points='0' WHERE summonerid LIKE '$summonerid' AND region LIKE '$region'");
	}
    }elseif((time() - strtotime($data->lastupdate) + (get_option('gmt_offset') * 3600)) > (15 * 60)){
        $json = @file_get_contents('https://' . $endpoint . '.api.pvp.net/api/lol/' . $region . '/v2.4/league/by-summoner/' . $summonerid . '/entry?api_key=' . $myKey);
	if($json != false){
		$obj = json_decode($json);
       		$summonername = '';
		$league = '';
       		$points = '';
		$hasLeague = false;
		foreach($obj->$summonerid as $queue){
				if($queue->queue == 'RANKED_SOLO_5x5'){
       	                		$summonername = $queue->entries[0]->playerOrTeamName;
					$league = $queue->tier . ' ' . $queue->entries[0]->division;
       	                		$points = $queue->entries[0]->leaguePoints;
					$hasLeague = true;
					break;
				}
		}
		if($hasLeague){
			$wpdb->query("UPDATE wp_lolshortcodes SET summonername='$summonername', league='$league', points='$points' WHERE summonerid LIKE '$summonerid' AND region LIKE '$region'");
		}else{
			$json = @file_get_contents('https://' . $endpoint . '.api.pvp.net/api/lol/' . $region . '/v1.4/summoner/' . $summonerid . '?api_key=' . $myKey);
			$obj = json_decode($json);
			$summonername = $obj->$summonerid->name;
			$wpdb->query("UPDATE wp_lolshortcodes SET summonername='$summonername', league='UNRANKED', points='0' WHERE summonerid LIKE '$summonerid' AND region LIKE '$region'");
		}
	}else{
		$json = @file_get_contents('https://' . $endpoint . '.api.pvp.net/api/lol/' . $region . '/v1.4/summoner/' . $summonerid . '?api_key=' . $myKey);
		$obj = json_decode($json);
		$summonername = $obj->$summonerid->name;
		$wpdb->query("UPDATE wp_lolshortcodes SET summonername='$summonername', league='UNRANKED', points='0' WHERE summonerid LIKE '$summonerid' AND region LIKE '$region'");
	}
    }
    $data = $wpdb->get_row("SELECT * FROM wp_lolshortcodes WHERE summonerid LIKE '$summonerid' AND region LIKE '$region'");
    return $data;
}

/* NOT AVAILABLE YET!!!

function current_game($atts) {
   extract(shortcode_atts(array(
      'summonername' => 'SoBiT',
      'region' => 'euw',
   ), $atts));
$json = file_get_contents('http://legendaryapi.com/api/v1.0/' . $region . '/summoner/retrieveInProgressSpectatorGameInfo/' . $summonername . '?authentication=9d36744aca3f1144bd147223da70ca595feeb30d');
$obj = json_decode($json);
if($obj->error == "Could not find any data.."){
    $return = $summonername . ' is currently not playing!';
}else{
    $return = '';
    
    //Blue Team
    $return = $return . '<div style="background:linear-gradient(#0381BB, #023E59); float:left; width:100%;">';
    foreach($obj->game->teamOne->array as $player){        
        foreach($obj->game->playerChampionSelections->array as $selection){
            if($player->summonerInternalName == $selection->summonerInternalName){
                $return = $return . '<div style="width:19%; text-align:center; margin: 0.5%; float:left; color:#ffffff; position:relative;"><img src="http://lkimg.zamimg.com/shared/riot/images/champions/loading_screens/' . $selection->championId . '_0.jpg" style="width:100%; height:auto;" /><div style="position:absolute; bottom:0; width:100%; background-color:rgba(0,0,0,0.5); padding-top:5%;"><img src="' . plugin_dir_url(__FILE__) . 'images/spells/' . $selection->spell1Id . '.png" style="width:20%; height:auto;" /> <img src="' . plugin_dir_url(__FILE__) . 'images/spells/' . $selection->spell2Id . '.png" style="width:20%; height:auto;" /><br/>';
            }
        }
        $return = $return . $player->summonerName.'</div></div>';
    }
    $return = $return . '</div>';
    
    //VERSUS
    $return = $return . '<br/><div style="color:#ffffff; font-size:500%; text-align:center; width:100%; float:left; padding: 2% 0;">VS.</div><br/>';
    
    //Purple Team
    $return = $return . '<div style="background:linear-gradient(#814092, #44224D); float:left; width:100%;">';
    foreach($obj->game->teamTwo->array as $player){        
        foreach($obj->game->playerChampionSelections->array as $selection){
            if($player->summonerInternalName == $selection->summonerInternalName){
                $return = $return . '<div style="width:19%; text-align:center; margin: 0.5%; float:left; color:#ffffff; position:relative;"><img src="http://lkimg.zamimg.com/shared/riot/images/champions/loading_screens/' . $selection->championId . '_0.jpg" style="width:100%; height:auto;" /><div style="position:absolute; bottom:0; width:100%; background-color:rgba(0,0,0,0.5); padding-top:5%;"><img src="' . plugin_dir_url(__FILE__) . 'images/spells/' . $selection->spell1Id . '.png" style="width:20%; height:auto;" /> <img src="' . plugin_dir_url(__FILE__) . 'images/spells/' . $selection->spell2Id . '.png" style="width:20%; height:auto;" /><br/>';
            }
        }
        $return = $return . $player->summonerName.'</div></div>';
    }
    $return = $return . '</div>';
}
return $return;
}

END OF CURRENT GAME*/

function install_db(){
	global $wpdb;

	$sql = "CREATE TABLE IF NOT EXISTS wp_lolshortcodes(
  	id int AUTO_INCREMENT,
  	summonerid text,
	region text,
        summonername text,
	level text,
 	league text,
	points text,
        lastupdate timestamp,
	PRIMARY KEY (id)
	);";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

register_activation_hook( __FILE__, 'install_db');
add_shortcode('summonername', 'summoner_name');
add_shortcode('points', 'summoner_points');
add_shortcode('league', 'league');
add_shortcode('score', 'lol_score');

// NOT AVAILABLE YET
//add_shortcode('ingame', 'current_game');

//////////////// ADMIN THINGS

// add the admin options page
add_action('admin_menu', 'plugin_admin_add_page');
function plugin_admin_add_page() {
	add_options_page('LoL Shortcodes', 'LoL Shortcodes', 'manage_options', 'lolsc', 'lolsc_options_page');
}

// display the admin options page
function lolsc_options_page() {
	echo '<div><h2>LoL Shortcodes</h2>This plugin provides shortcodes related to League of Legends<form action="options.php" method="post">';

	settings_fields('lolsc_options');
	do_settings_sections('lolsc');

	echo '<input name="Submit" type="submit" class="button button-primary" value="Save Changes" /></form></div>';
}
// add the admin settings and such
add_action('admin_init', 'lolsc_admin_init');
function lolsc_admin_init(){
	register_setting('lolsc_options', 'lolsc_options', 'lolsc_options_validate');
	add_settings_section('lolsc_main', 'LoL Shortcodes API Key', 'lolsc_section_text', 'lolsc');
	add_settings_field('lolsc_apikey', 'Your API Key', 'lolsc_setting_output', 'lolsc', 'lolsc_main');
}

function lolsc_section_text() {
	echo '<p>Enter your API Key to get access to the League of Legends database. If you don\'t have one yet, get your own by registering here: <a href="http://developer.riotgames.com/" target="_blank">developer.riotgames.com</a></p>';
}

function lolsc_setting_output() {
	$options = get_option('lolsc_options');
	echo "<input id='lolsc_apikey' name='lolsc_options[apikey]' size='36' type='text' value='" . $options['apikey'] . "' />";
}

// validate our options
function lolsc_options_validate($input) {
$options = get_option('lolsc_options');
$options['apikey'] = trim($input['apikey']);
if(!preg_match('/^[a-z0-9\-]{36}$/i', $options['apikey'])) {
$options['apikey'] = '';
}
return $options;
}
?>