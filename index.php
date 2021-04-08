<?php
session_start();
//Get configuration and language
require 'config.php';
require 'language/lang_'.$conf_language.'.php';
require_once 'system/phpcount.php';
require_once 'system/DbConnMain.php';
require_once 'system/cryptor.php';
//Import PHP plugin code
include 'plugins/php_before_html.php';
//Visitor counter and security
$php_count = new PHPCount;
$crypt = new Cryptor;
$php_count->AddHit("index",$mysql_host,$mysql_user,$mysql_pass,$mysql_dbname);
$conn = DbConnMain::connect($mysql_host,$mysql_dbname,$mysql_user,$mysql_pass);
function suggest_dialog_check($conf_movie_suggest_logged_only){
	if($conf_movie_suggest_logged_only == false){
		return true;
	} else if(isset($_SESSION['username'])){
		return true;
	} else {
		return false;
	}
}
//Thay thế 
$category_data = array(	"cat-comedy"=>$lang_category_comedy_button,
	"cat-documentary"=>$lang_category_documentary_button,
	"cat-detective"=>$lang_category_detective_button,
	"cat-romantic"=>$lang_category_romantic_button,
	"cat-adventure"=>$lang_category_adventure_button,
	"cat-horror"=>$lang_category_horror_button,
	"cat-fantasy"=>$lang_category_fantasy_button,
	"cat-biography"=>$lang_category_biography_button,
	"cat-sport"=>$lang_category_sport_button,
	"cat-action"=>$lang_category_action_button,
	"cat-mystic"=>$lang_category_mystic_button,
	"cat-war"=>$lang_category_war_button,
	"cat-thriller"=>$lang_category_thriller_button,
	"cat-family"=>$lang_category_family_button,
	"cat-crime"=>$lang_category_crime_button,
	"cat-western"=>$lang_category_western_button,
	"cat-music"=>$lang_category_music_button,
	"cat-history"=>$lang_category_history_button,
	"cat-science"=>$lang_category_science_button,
	"cat-drama"=>$lang_category_drama_button);
$type_data = array("type-movie"=>$lang_media_type_movie,"type-sers"=>$lang_media_type_serial,"type-anim"=>$lang_media_type_anim,"type-tv"=>$lang_media_type_tv);
function getMediaGenres($genre_array,$data){
	$rp_data = array();
	foreach($genre_array as $x => $x_value){
		array_push($rp_data,$data[$x_value]);
	}
	return implode(", ",$rp_data);
}
function getMediaData($type,$genres,$props,$id,$watched,$favorite){
	$data_array = array();
	array_push($data_array,"all",$type);
	foreach($genres as $x => $x_value){
		array_push($data_array,$x_value);
	}
	foreach($props as $y => $y_value){
		array_push($data_array,$y_value);
	}
	$watched_array = explode(",",$watched);
	$favorite_array = explode(",",$favorite);
	if(in_array($id,$watched_array)){
		array_push($data_array,"my-watched");
	}
	if(in_array($id,$favorite_array)){
		array_push($data_array,"my-favorite");
	}
	return "[\"".implode('", "',$data_array)."\"]";
}
//Check for empty data
function isDataSet($str){
	if($str != "_no_data" && $str != null && $str != ""){
		return true;
	} else {
		return false;
	}
}
?>
