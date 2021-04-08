<?php
session_start();
require 'config.php';
require 'language/lang_'.$conf_language.'.php';
require_once 'system/JBBCode/Parser.php';
require_once 'system/DbConnMain.php';
require_once 'system/cryptor.php';
include 'plugins/php_before_html.php';
$crypt = new Cryptor;
$conn = DbConnMain::connect($mysql_host,$mysql_dbname,$mysql_user,$mysql_pass);
function loginForm() {
	echo '
	<div class="form-group">
	<div id="loginform">
	<form action="media.php?id=chan-nho-ban-o-dau" method="post">
	<h1>Live Chat</h1><hr/>
	<label for="name">Vui lòng nhập tên của bạn để tiếp tục..</label>
	<input type="text" name="name" id="name" class="form-control" placeholder="Nhập tên của bạn"/>
	<input type="submit" class="btn btn-default" name="enter" id="enter" value="Gửi" />
	</form>
	</div>
	</div>
	';
}

if (isset ( $_POST ['enter'] )) {
	if ($_POST ['name'] != "") {
		$_SESSION ['name'] = stripslashes ( htmlspecialchars ( $_POST ['name'] ) );
		$cb = fopen ( "log.html", 'a' );
		fwrite ( $cb, "<div class='msgln'><i>Người dùng " . $_SESSION ['name'] . " đã tham gia trò chuyện.</i><br></div>" );
		fclose ( $cb );
	} else {
		echo '<span class="error">Vui lòng nhập tên</span>';
	}
}

if (isset ( $_GET ['logout'] )) {
	$cb = fopen ( "log.html", 'a' );
	fwrite ( $cb, "<div class='msgln'><i>Người dùng " . $_SESSION ['name'] . " đã thoát trò chuyện.</i><br></div>" );
	fclose ( $cb );
	session_destroy ();
	header ( "Location: media.php?id=chan-nho-ban-o-dau" );
}
function suggest_dialog_check($conf_movie_suggest_logged_only){
	if($conf_movie_suggest_logged_only == false){
		return true;
	} else if(isset($_SESSION['username'])){
		return true;
	} else {
		return false;
	}
}
function report_dialog_check($conf_movie_report_logged_only){
	if($conf_movie_report_logged_only == false){
		return true;
	} else if(isset($_SESSION['username'])){
		return true;
	} else {
		return false;
	}
}
function isDataSet($str){
	if($str != "_no_data" && $str != null && $str != ""){
		return true;
	} else {
		return false;
	}
}
function isYoutube($str){
	if(strpos($str,'youtube.com') || strpos($str,'youtu.be')){
		return true;
	} else {
		return false;
	}
}
function isVimeo($str){
	if(strpos($str,'vimeo.com')){
		return true;
	} else {
		return false;
	}
}
function isHls($str){
	if(strpos($str,'.m3u8')){
		return true;
	} else {
		return false;
	}
}
function isHtml($str){
	if(strpos($str,'[/html]')){
		return true;
	} else {
		return false;
	}
}
function get_string_between($string, $start, $end){
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
function getVideoId($str){
	$id = "";
	if(isYoutube($str)){
		if(preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $str, $match)){
			$id = $match[1];
		} else {
			$id = "111111";
		}
	} elseif(isVimeo($str)){
		if(preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $str, $match)){
			$id = $match[3];
		} else {
			$id = "111111";
		}
	}
	return $id;
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
//Thay thế
$property_data = array(	"filter-speak"=>$lang_filter_drop_with_speak,
	"filter-subtitles"=>$lang_filter_drop_with_subtitles,
	"filter-uhd"=>$lang_filter_drop_hq,
	"filter-3d"=>$lang_filter_drop_3d,
	"filter-360"=>$lang_filter_drop_360,
	"_no_data"=>"_no_data");
function getMediaGenres($genre_array,$data){
	$rp_data = array();
	foreach($genre_array as $x => $x_value){
		array_push($rp_data,$data[$x_value]);
	}
	return implode(", ",$rp_data);
}
function getMediaProps($prop_array,$data){
	$rp_data = array();
	foreach($prop_array as $x => $x_value){
		array_push($rp_data,$data[$x_value]);
	}
	return implode(", ",$rp_data);
}
$type_data = array("type-movie"=>$lang_media_type_movie,
	"type-sers"=>$lang_media_type_serial,
	"type-anim"=>$lang_media_type_anim,
	"type-tv"=>$lang_media_type_tv);

$current_media_url = ((isset($_SERVER['HTTPS']) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === "https")) ? "https" : "http")."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$bb_parser = new JBBCode\Parser();
$bb_parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
?>
<!doctype html>
<?php
if(isset($_GET["id"])){
	$movie_id = addslashes($_GET["id"]);
	try {
		$gstmt = $conn->prepare("SELECT * FROM ".$table_movies." WHERE `media_url`='".$movie_id."'");
		$gstmt->execute();
		$gresult = $gstmt->setFetchMode(PDO::FETCH_ASSOC);
		if($gstmt->rowCount() != 0){
			foreach($gstmt->fetchAll() as $grow){
				$get_categories = array_diff(explode("=div=",$grow['media_genre']), array('_no_data'));
				$movie_genres = getMediaGenres($get_categories,$category_data);
				$movie_properties = getMediaProps(explode("=div=",$grow['media_properties']),$property_data);
				?>
				<html>
				<head>
					<?php
					$title_vars = ["%movie","%movie_desc","%site_name","%global_title","%media_type"];
					$title_data = [$grow['media_name'],$grow['media_desc'],$conf_site_name,$conf_site_title,$type_data[$grow['media_type']]];
					$returned_title = str_replace($title_vars,$title_data,$lang_media_site_title);
					?>
					<link rel="shortcut icon" type="images/png" href="images/favicon.png"/>
					<title><?php echo $returned_title ?></title>
					<meta charset="UTF-8">
					<meta name="description" content="<?php echo str_replace('"','&quot;',strip_tags($grow['media_desc'])) ?>">
					<meta name="keywords" content="<?php echo $conf_meta_keywords ?>">
					<meta name="theme-color" content="#1c1c1c">
					<meta name="viewport" content="width=device-width, initial-scale=0">
					<link rel="stylesheet" href="styles/material.min.css">
					<link rel="stylesheet" href="styles/custom.css">
					<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
					<link rel="stylesheet" href="styles/simplebar.css" />
					<link rel="stylesheet" href="styles/style.css" />
					<link rel="stylesheet" type="text/css" href="styles/dialog-polyfill.css" />
					<link rel="stylesheet" href="plyr/plyr.css">
					<style id="dyn-style"></style>
					<script src="styles/material.min.js"></script>
					<script src="styles/simplebar.js"></script>
					<script src="scripts/jquery-3.3.1.min.js"></script>
					<?php include 'plugins/head.php'; ?>
				</head>
				<body>
					<!-- Tiêu đề cuộn Phim đề cử -->
					<div class="right-sidebar" data-simplebar>
						<div class="rs-header"><div style="padding-top:22px;">PHIM ĐỀ CỬ</div></div>
						<?php
						try {
							$stmt = $conn->prepare("SELECT media_url, media_cover FROM ".$table_movies." ORDER BY RAND() LIMIT 5");
							$stmt->execute();
							$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
							foreach($stmt->fetchAll() as $row){
								?>
								<div onclick="window.location.href='media.php?id=<?php echo $row['media_url'] ?>'" class="side-imgh" style="background:url('<?php if(isDataSet($row['media_cover'])){ echo str_ireplace("http://","https://",$row['media_cover']); } else { echo "images/no_cover_img.png"; } ?>') center center / cover;"></div>
								<?php
							}
						}
						catch(PDOException $e) {
							echo "MySQL connection failed: " . $e->getMessage();
						}
						?>
						<div style="margin-bottom:28px;"></div>
					</div>
					<div class="mdl-layout mdl-js-layout" style="overflow-y:scroll !important;">
						<header class="mdl-layout__header mdl-layout__header--scroll" style="background-color: #1c1c1c;">
							<div class="mdl-layout__header-row">
								<!-- Title -->
								<a href="index.php"><img src="images/logophimhay.png" style="width:120px;"></a>
								<div class="header-buttons" id="header-buttons-id">
									<?php if(!isset($_SESSION['username'])){ ?>
										<button id="show-login-dialog" class="mdl-button mdl-js-button" style="color: #949494; font-weight: bold; margin-left: 50px;"><i class="material-icons" style="color:#1e4eb9;margin-right:5px;">account_circle</i>Đăng nhập</button>
									<?php } else { ?>
										<button id="show-account-menu" class="mdl-button mdl-js-button" style="color: #e1e1e1; font-weight: bold; margin-left: 50px; height: 100% !important; border-radius: 0px !important; padding: 14px 16px !important;">
											<div id="menuAvatarImage" style="border-radius:50%;width:32px;height:32px;margin-right:15px;margin-top:2px;float:left;background:url('<?php echo $_SESSION['avatar_img'] ?>') center / cover;"></div><?php echo $_SESSION['username'] ?> <i class="material-icons">keyboard_arrow_down</i>
										</button>
										<ul class="mdl-menu mdl-menu--bottom-left mdl-js-menu mdl-js-ripple-effect" style="min-width:250px;background-color:#191c21 !important;" for="show-account-menu">
											<li id="show-myaccount-dialog" class="mdl-menu__item movie-category-li"><i class="material-icons account-menu-item">person</i><?php echo $lang_my_account_button ?></li>
											<?php if($crypt->decrypt($_SESSION['is_admin'],$secret_key,$secret_iv) == '1'){ ?>
												<li onclick="window.location.href='admin.php'" class="mdl-menu__item movie-category-li"><i class="material-icons account-menu-item">settings</i><?php echo $lang_admin_panel_button ?></li>
											<?php } ?>
											<li onclick="window.location.href='index.php?filter=my-watched'" class="mdl-menu__item movie-category-li"><i class="material-icons account-menu-item">done</i><?php echo $lang_watched_movies_button ?></li>
											<li onclick="window.location.href='index.php?filter=my-favorite'" class="mdl-menu__item movie-category-li"><i class="material-icons account-menu-item">favorite</i><?php echo $lang_favorite_movies_button ?></li>
											<li onclick="window.location.href='system/login/logout.php'" class="mdl-menu__item movie-category-li"><i class="material-icons account-menu-item">exit_to_app</i><?php echo $lang_logout_button ?></li>
										</ul>
									<?php } ?>
									<button id="<?php if(suggest_dialog_check($conf_movie_suggest_logged_only)){ echo 'show-suggest-dialog'; } else { echo 'show-suggest-toast'; } ?>" class="mdl-button mdl-js-button" style="color: #949494; font-weight: bold; margin-left: 5px;"><i class="material-icons" style="color:#0cbb94;margin-right:5px;">note_add</i> <?php echo $lang_suggest_movie_button ?></button>
									<button id="movie-categories-menu" class="mdl-button mdl-js-button" style="color: #949494; font-weight: bold; margin-left: 5px;"><i class="material-icons" style="color:#910d0d;margin-right:5px;">remove_red_eye</i> <?php echo $lang_movie_genres_button ?> <i class="material-icons">keyboard_arrow_down</i></button>
									<ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="movie-categories-menu" style="display:flex;background-color:#191c21 !important;padding:0 !important;width:750px !important;">
										<div class="movie-catg-col">
											<li onclick="window.location.href='index.php?filter=cat-comedy'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_comedy_button ?></li>
											<li onclick="window.location.href='index.php?filter=cat-documentary'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_documentary_button ?></li>
											<li onclick="window.location.href='index.php?filter=cat-detective'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_detective_button ?></li>
											<li onclick="window.location.href='index.php?filter=cat-romantic'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_romantic_button ?></li>
										</div>
										<div class="movie-catg-col">
											<li onclick="window.location.href='index.php?filter=cat-adventure'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_adventure_button ?></li>
											<li onclick="window.location.href='index.php?filter=cat-horror'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_horror_button ?></li>
											<li onclick="window.location.href='index.php?filter=cat-fantasy'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_fantasy_button ?></li>
											<li onclick="window.location.href='index.php?filter=cat-biography'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_biography_button ?></li>
										</div>
										<div class="movie-catg-col">
											<li onclick="window.location.href='index.php?filter=cat-sport'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_sport_button ?></li>
											<li onclick="window.location.href='index.php?filter=cat-action'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_action_button ?></li>
											<li onclick="window.location.href='index.php?filter=cat-mystic'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_mystic_button ?></li>
											<li onclick="window.location.href='index.php?filter=cat-war'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_war_button ?></li>
										</div>
										<div class="movie-catg-col">
											<li onclick="window.location.href='index.php?filter=cat-thriller'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_thriller_button ?></li>
											<li onclick="window.location.href='index.php?filter=cat-family'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_family_button ?></li>
											<li onclick="window.location.href='index.php?filter=cat-crime'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_crime_button ?></li>
											<li onclick="window.location.href='index.php?filter=cat-western'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_western_button ?></li>
										</div>
										<div class="movie-catg-col mcc-right">
											<li onclick="window.location.href='index.php?filter=cat-music'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_music_button ?></li>
											<li onclick="window.location.href='index.php?filter=cat-history'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_history_button ?></li>
											<li onclick="window.location.href='index.php?filter=cat-science'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_science_button ?></li>
											<li onclick="window.location.href='index.php?filter=cat-drama'" class="mdl-menu__item movie-category-li"><?php echo $lang_category_drama_button ?></li>
										</div>
									</ul>
								</div>
								<!-- Add spacer, to align navigation to the right -->
								<div class="mdl-layout-spacer"></div>
								<!-- Navigation -->
								<nav class="mdl-navigation">
									<span onclick="location.reload()" class="mdl-navigation__link" style="cursor:pointer;"><i class="material-icons">refresh</i></span>
									<a class="mdl-navigation__link" href="index.php"><i class="material-icons">home</i></a>
									<span onclick="window.location.href='index.php?search=true'" class="mdl-navigation__link" style="cursor:pointer;display:block;" id="show-search-btn"><i class="material-icons">search</i></span>
								</nav>
							</div>
						</header>
						<div class="mdl-layout__drawer">
							<span class="mdl-layout-title"><?php echo $conf_site_name ?></span>
							<nav class="mdl-navigation">
								<span style="cursor:pointer !important;" id="show-rules-dialog" class="mdl-navigation__link">Điều khoản dịch vụ</span>
								<span style="cursor:pointer !important;" id="show-contact-dialog" class="mdl-navigation__link">Liên hệ</span>
								<span style="cursor:pointer !important;" id="show-policy-dialog" class="mdl-navigation__link">Chính sách bảo mật</span>
								<span style="cursor:pointer !important;" id="show-about-dialog" class="mdl-navigation__link">Về chúng tôi</span>
							</nav>
						</div>
						<main class="mdl-layout__content">
							<div class="page-content">
								<div style="position:absolute;background-color:#1c1c1c;width:70px;height:100%;"></div>
								<div class="mdc-header">
									<button onclick="window.location.href='index.php'" class="mdl-button mdl-js-button" style="color:#c7c7c7;border:1px solid #3e3e3e;margin-left:25px;"><i class="material-icons">arrow_back</i> <?php echo $lang_media_main_back_button ?></button>
									<button id="<?php if(report_dialog_check($conf_movie_report_logged_only)){ echo 'show-report-dialog'; } else { echo 'show-report-toast'; } ?>" class="mdl-button mdl-js-button" style="color:#b8312f;float:right;border:1px solid #b8312f;margin-right:145px;"><i class="material-icons">report</i> <?php echo $lang_media_main_report_button ?></button>
									<button onclick="window.open('https://plus.google.com/share?url=<?php echo $current_media_url ?>','_blank','width=800,height=500')" id="google-share-btn" class="mdl-button mdl-js-button" style="color:#fff;float:right;background-color:#dc4e41;"><svg style="width:24px;height:24px" viewBox="0 0 24 24"> <path fill="#fff" d="M23,11H21V9H19V11H17V13H19V15H21V13H23M8,11V13.4H12C11.8,14.4 10.8,16.4 8,16.4C5.6,16.4 3.7,14.4 3.7,12C3.7,9.6 5.6,7.6 8,7.6C9.4,7.6 10.3,8.2 10.8,8.7L12.7,6.9C11.5,5.7 9.9,5 8,5C4.1,5 1,8.1 1,12C1,15.9 4.1,19 8,19C12,19 14.7,16.2 14.7,12.2C14.7,11.7 14.7,11.4 14.6,11H8Z"/></svg></button>
									<button onclick="window.open('https://twitter.com/share?url=<?php echo $current_media_url ?>&amp;text=<?php echo str_replace("%a",$grow['media_name'],$lang_media_sharing_text) ?>','_blank','width=800,height=500')" id="twitter-share-btn" class="mdl-button mdl-js-button" style="color:#fff;float:right;background-color:#1da1f2;"><svg style="width:24px;height:24px" viewBox="0 0 24 24"> <path fill="#fff" d="M22.46,6C21.69,6.35 20.86,6.58 20,6.69C20.88,6.16 21.56,5.32 21.88,4.31C21.05,4.81 20.13,5.16 19.16,5.36C18.37,4.5 17.26,4 16,4C13.65,4 11.73,5.92 11.73,8.29C11.73,8.63 11.77,8.96 11.84,9.27C8.28,9.09 5.11,7.38 3,4.79C2.63,5.42 2.42,6.16 2.42,6.94C2.42,8.43 3.17,9.75 4.33,10.5C3.62,10.5 2.96,10.3 2.38,10C2.38,10 2.38,10 2.38,10.03C2.38,12.11 3.86,13.85 5.82,14.24C5.46,14.34 5.08,14.39 4.69,14.39C4.42,14.39 4.15,14.36 3.89,14.31C4.43,16 6,17.26 7.89,17.29C6.43,18.45 4.58,19.13 2.56,19.13C2.22,19.13 1.88,19.11 1.54,19.07C3.44,20.29 5.7,21 8.12,21C16,21 20.33,14.46 20.33,8.79C20.33,8.6 20.33,8.42 20.32,8.23C21.16,7.63 21.88,6.87 22.46,6Z"/></svg></button>
									<button onclick="window.open('http://www.facebook.com/sharer.php?u=<?php echo $current_media_url ?>','_blank','width=800,height=500')" id="facebook-share-btn" class="mdl-button mdl-js-button" style="color:#fff;float:right;background-color:#3b5998;"><svg style="width:24px;height:24px" viewBox="0 0 24 24"> <path fill="#fff" d="M17,2V2H17V6H15C14.31,6 14,6.81 14,7.5V10H14L17,10V14H14V22H10V14H7V10H10V6A4,4 0 0,1 14,2H17Z"/></svg></button>
								</div>
								<div class="movie-content movie-watch-content">
									<div class="movie-cover-side">
										<div class="movie-cover-img" style="background:url('<?php if(isDataSet($grow['media_cover'])){ echo str_ireplace("http://","https://",$grow['media_cover']); } else { echo "images/no_cover_img.png"; } ?>') center center / cover;"></div>
										<button onclick="actionViewMedia(<?php echo $grow['id'] ?>)" id="show-watch-movie-dialog" class="mdl-button mdl-js-button" style="color:#f4f5f7;font-weight:bold;width:100%;height:45px !important;background-color:#00a8e6;margin-top:25px;"><i class="material-icons" style="margin-right:5px;">tv</i>Xem phim</button>
										<button id="likeMediaButtonId" onclick="likeMedia(<?php echo $grow['id'] ?>,'<?php echo $grow['media_name'] ?>')" class="mdl-button mdl-js-button" style="color:#0db791;font-weight:bold;width:100%;height:45px !important;border:1px solid #186e5b;margin-top:15px;">
											<?php $likes_check = explode(",",(isset($_SESSION['my_liked_media']) ? $_SESSION['my_liked_media'] : "")); if(!in_array($grow['id'],$likes_check)){ ?>
												<i class="material-icons" style="margin-right:5px;">thumb_up</i> Thích phim
											<?php } else { ?>
												<i class="material-icons" style="margin-right:5px;">done</i> Đã thích! +1
											<?php } ?>
										</button>
										<button id="addToFavsButtonId" onclick="addToFavorites(<?php echo $grow['id'] ?>)" class="mdl-button mdl-js-button" style="color:#999999;font-weight:bold;width:100%;height:45px !important;margin-top:15px;">
											<?php $favorites_check = explode(",",(isset($_SESSION['my_favorite_media']) ? $_SESSION['my_favorite_media'] : "")); if(!in_array($grow['id'],$favorites_check)){ ?>
												<i class="material-icons" style="margin-right:5px;">favorite</i>Thêm vào mục yêu thích
											<?php } else { ?>
												<i class="material-icons" style="margin-right:5px;">clear</i>Loại bỏ khỏi mục ưa thích
											<?php } ?>
										</button>
										<!--Live chat-->
										<?php
										if (! isset ( $_SESSION ['name'] )) {
											loginForm ();
										} else {
											?>
											<div id="wrapper">
												<div id="menu">
													<h1>Live Chat!</h1><hr/>
													<p class="welcome"><b>HI - <a><?php echo $_SESSION['name']; ?></a></b></p>
													<p class="logout"><a id="exit" href="#" class="btn btn-default">Thoát Live Chat</a></p>
													<div style="clear: both"></div>
												</div>
												<div id="chatbox">
													<?php
													if (file_exists ( "log.html" ) && filesize ( "log.html" ) > 0) {
														$handle = fopen ( "log.html", "r" );
														$contents = fread ( $handle, filesize ( "log.html" ) );
														fclose ( $handle );

														echo $contents;
													}
													?>
												</div>
												<form name="message" action="">
													<input name="usermsg" class="form-control" type="text" id="usermsg" placeholder="Nhập vào một tin nhắn" />
													<input name="submitmsg" class="btn btn-default" type="submit" id="submitmsg" value="Gửi" />
												</form>
											</div>

											<?php
										}
										?>
										<!--Live chat-->

									</div>
									<div class="movie-infotabs-side">
										<div class="tabs-bar-flex">
											<button class="tablink" onclick="openPage('cn_description', this, '#249af2')" id="defaultOpen">Mô tả</button>
											<button class="tablink" onclick="openPage('cn_reviews', this, '#249af2')">Nhận xét</button>
											<button class="tablink" onclick="openPage('cn_trailer', this, '#249af2')">Trailer</button>
										</div>
										<div id="cn_description" class="tabcontent">
											<div class="watch-media-name"><?php echo $grow['media_name'] ?></div>
											<div class="movie-details-box">
												<div><span><i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;color:#056899;">thumb_up</i> <?php echo $grow['media_likes'] ?></span></div>
												<div><span><i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;color:#11915a;">remove_red_eye</i> <?php echo $grow['media_views'] ?></span></div>
												<?php if(isDataSet($grow['media_duration'])){ ?><div><span><i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;">access_time</i> <?php echo $grow['media_duration'] ?></span></div><?php } ?>
												<div><span><i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;">date_range</i> <?php echo $grow['media_upload_date'] ?></span></div>
												<?php if(isDataSet($movie_genres)){ ?><div><span><i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;">theaters</i> <?php echo $movie_genres ?></span></div><?php } else { ?><div><span><i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;">videocam</i> <?php echo $type_data[$grow['media_type']] ?></span></div><?php } ?>
												<?php if(isDataSet($grow['media_language'])){ ?><div><span><i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;">language</i> <?php echo $grow['media_language'] ?></span></div><?php } ?>
												<?php if(isDataSet($movie_properties)){ ?><div><span><i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;">equalizer</i> <?php echo $movie_properties ?></span></div><?php } ?>
											</div>
											<div class="movie-description-box"><?php echo $grow['media_desc'] ?></div>
											<div class="movie-description-box" style="margin-top:20px;">
												<table class="creators-table">
													<tbody>
														<tr>
															<td><?php echo $lang_media_info_media_type_title ?></td>
															<td><?php echo $type_data[$grow['media_type']] ?></td>
														</tr>
														<?php if(isDataSet($grow['media_released'])){ ?>
															<tr>
																<td><?php echo $lang_media_info_release_date_title ?></td>
																<td><?php echo $grow['media_released'] ?></td>
															</tr>
														<?php } ?>
														<?php if(isDataSet($movie_genres)){ ?>
															<tr>
																<td><?php echo $lang_media_info_genre_title ?></td>
																<td><?php echo $movie_genres ?></td>
															</tr>
														<?php } ?>
														<?php if(isDataSet($grow['media_actors'])){ ?>
															<tr>
																<td><?php echo $lang_media_info_actors_title ?></td>
																<td><?php echo $grow['media_actors'] ?></td>
															</tr>
														<?php } ?>
														<?php if(isDataSet($grow['media_director'])){ ?>
															<tr>
																<td><?php echo $lang_media_info_director_title ?></td>
																<td><?php echo $grow['media_director'] ?></td>
															</tr>
														<?php } ?>
														<?php if(isDataSet($grow['media_leaders'])){ ?>
															<tr>
																<td><?php echo $lang_media_info_leaders_title ?></td>
																<td><?php echo $grow['media_leaders'] ?></td>
															</tr>
														<?php } ?>
														<?php if(isDataSet($grow['media_other_info'])){ ?>
															<tr>
																<td><?php echo $lang_media_info_other_information_title ?></td>
																<td><?php echo $grow['media_other_info'] ?></td>
															</tr>
														<?php } ?>
													</tbody>
												</table>
											</div>
										</div>
										<div id="cn_reviews" class="tabcontent">
											<div style="font-size:19px;line-height:25px;margin-bottom:15px;">Gửi đánh giá</div>
											<?php if(!isset($_SESSION['username'])){ ?>
												<div class="login-req-message-warn" style="background-color:#da932b;"><div style="color:rgba(255,255,255,0.8);font-size:16px;padding-top:15px;padding-left:15px;"><i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;">warning</i>Chỉ người dùng đăng nhập mới có thể đăng đánh giá.<i class="material-icons" style="float:right;margin:-2px 15px 0px 0px;color:rgba(255,255,255,0.23);cursor:pointer;" onclick="closeWarnMessage()">clear</i></div></div>
											<?php } else { ?>
												<div class="login-req-message-warn" style="background-color:#16a086;"><div style="color:rgba(255,255,255,0.8);font-size:16px;padding-top:15px;padding-left:15px;"><i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;">done</i>Bạn đồng ý với Điều khoản dịch vụ bằng cách đăng đánh giá.<i class="material-icons" style="float:right;margin:-2px 15px 0px 0px;color:rgba(255,255,255,0.23);cursor:pointer;" onclick="closeWarnMessage()">clear</i></div></div>
											<?php } ?>
											<form id="postReviewFormId" method="post" name="ReviewForm" onsubmit="return validateReviewForm()" action="system/functions/user_actions.php">
												<input type="hidden" name="media_id" value="<?php echo $grow['id'] ?>">
												<div class="mdl-textfield mdl-js-textfield" style="width:100% !important;margin-top:10px;">
													<textarea class="mdl-textfield__input" type="text" name="post_review" id="rwr_a" style="border-bottom:1px solid #3b3b3b !important;min-height:120px;"></textarea>
													<label class="mdl-textfield__label" for="rwr_a" style="color:#b7b4b4;"><?php echo $lang_media_reviews_field_label ?></label>
												</div>
												<div style="color:#999999;float:left;">Bạn có thể dùng <span style=\"color:#b71318;\">BB</span> định dạng mã.</div>
												<button type="submit" class="mdl-button mdl-js-button" style="background-color:#27ae61;color:#f4f5f7;font-weight:bold;float:right;">Đăng</button>
											</form>
											<div class="reviews-box" data-simplebar>
												<?php
												try {
													$stmt = $conn->prepare("SELECT * FROM ".$table_reviews." ORDER BY id DESC");
													$stmt->execute();
													$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
													if($stmt->rowCount() != 0){
														$movie_review_count = 0;
														foreach($stmt->fetchAll() as $row){
															if($grow['id'] == $row['media_id']){
																$movie_review_count++;
																$write_date = date_create($row['write_date']);
																$today_date = date_create(date($date_format));
																$diff = date_diff($write_date,$today_date)->format("%a");
																if($diff == 0){
																	$returned_date = $lang_media_reviews_date_today;
																} elseif($diff == 1){
																	$returned_date = str_replace("%a",$diff,$lang_media_reviews_date_day);
																} elseif($diff < 10) {
																	$returned_date = str_replace("%a",$diff,$lang_media_reviews_date_days);
																} else {
																	$returned_date = $row['write_date'];
																}
																$avatar_image = "images/img_avatar.png";
																$bb_parser->parse($row['message']);
																$returned_message = $bb_parser->getAsHtml();
																$stmt2 = $conn->prepare("SELECT banned, avatar_img FROM ".$table_members." WHERE `username`='".$row['username']."'");
																$stmt2->execute();
																$result2 = $stmt2->setFetchMode(PDO::FETCH_ASSOC);
																if($stmt2->rowCount() != 0){
																	foreach($stmt2->fetchAll() as $brow){
																		$avatar_image = $brow['avatar_img'];
																		if($brow['banned'] == '1'){
																			$returned_message = $lang_media_reviews_banned_user;
																		}
																	}
																} else {
																	$returned_message = $lang_media_reviews_deleted_user;
																}
																?>
																<div class="comment-box">
																	<div class="comment-author-info">
																		<div class="comment-author-avatar" style="background:url('<?php echo $avatar_image ?>') center / cover;"></div>
																		<div class="comment-author-username">@<?php echo $row['username'] ?></div>
																		<div class="comment-write-date"><?php echo $returned_date ?></div>
																	</div>
																	<div class="comment-text-content"><?php echo $returned_message ?></div>
																</div>
																<?php
															}
														}
														if($movie_review_count == 0){
															?>
															<div style="text-align:center;">Chưa có đánh giá nào...</div>
															<?php
														}
													} else {
														?>
														<div style="text-align:center;">Chưa có đánh giá nào...</div>
														<?php
													}
												}
												catch(PDOException $e) {
													echo "MySQL connection failed: " . $e->getMessage();
												}
												?>
											</div>
										</div>
										<div id="cn_trailer" class="tabcontent">
											<?php if(isDataSet($grow['media_trailer'])){ if(isYoutube($grow['media_trailer'])){ ?>
												<div id="trailer-loaded"><div id="trailer-video" data-plyr-provider="youtube" data-plyr-embed-id="<?php echo getVideoId($grow['media_trailer']) ?>" <?php if($conf_watch_trailer_ads && $conf_vi_ai_publisher_id != ""){echo 'data-plyr-config=\'{ "ads": { enabled: true, publisherId: \''.$conf_vi_ai_publisher_id.'\' } }\'';} ?>></div></div>
											<?php } elseif(isVimeo($grow['media_trailer'])){ ?>
												<div id="trailer-loaded"><div id="trailer-video" data-plyr-provider="vimeo" data-plyr-embed-id="<?php echo getVideoId($grow['media_trailer']) ?>" <?php if($conf_watch_trailer_ads && $conf_vi_ai_publisher_id != ""){echo 'data-plyr-config=\'{ "ads": { enabled: true, publisherId: \''.$conf_vi_ai_publisher_id.'\' } }\'';} ?>></div></div>
											<?php } else { ?>
												<div id="trailer-loaded">
													<video id="trailer-video" controls crossorigin playsinline <?php if($conf_use_custom_poster){echo 'poster="'.$video_poster_image_url.'"';} ?> <?php if($conf_watch_trailer_ads && $conf_vi_ai_publisher_id != ""){echo 'data-plyr-config=\'{ "ads": { enabled: true, publisherId: \''.$conf_vi_ai_publisher_id.'\' } }\'';} ?>>
														<source src="<?php echo $grow['media_trailer'] ?>" type="video/mp4">
															<div class="login-req-message-warn"><div style="color:#f3ebd7;font-size:16px;padding-top:15px;padding-left:15px;margin-top:20px;"><i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;">warning</i>Trình duyệt của bạn không hỗ trợ video HTML5, vui lòng cập nhật trình duyệt.</div></div>
														</video>
													</div>
												<?php }} else { ?>
													<div style="text-align:center;">Không có trailer</div>
												<?php } ?>
											</div>
											<div class="recommended-movies-box">
												<div class="recom-movies-title"><div style="padding-top:20px;">Có thể bạn thích</div></div>
												<?php
												try {
													$stmt = $conn->prepare("SELECT * FROM ".$table_movies." ORDER BY RAND() LIMIT 3");
													$stmt->execute();
													$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
													foreach($stmt->fetchAll() as $row){
														$rget_categories = array_diff(explode("=div=",$row['media_genre']), array('_no_data'));
														$rget_genres = getMediaGenres($rget_categories,$category_data);
														$rmovie_genres = '<i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;">theaters</i> '.$rget_genres;
														if($rget_genres == null || $rget_genres == ""){
															$rmovie_genres = '<i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;">videocam</i> '.$type_data[$row['media_type']];
														}
														?>
														<div onclick="window.location.href='media.php?id=<?php echo $row['media_url'] ?>'" class="recom-movie-box">
															<div class="recom-movie-cover" style="background:url('<?php if(isDataSet($row['media_cover'])){ echo str_ireplace("http://","https://",$row['media_cover']); } else { echo "images/no_cover_img.png"; } ?>') center center / cover;"></div>
															<div class="recom-movie-title"><?php echo $row['media_name'] ?></div>
															<div class="recom-movie-description line-clamp line-clamp-3"><?php echo $row['media_desc'] ?></div>
															<div class="recom-movie-textl"><?php if(isDataSet($row['media_director'])){ echo $lang_index_movie_director_title." ".$row['media_director']; } elseif(isDataSet($row['media_leaders'])){ echo $lang_index_movie_leaders_title." ".$row['media_leaders']; } elseif(isDataSet($row['media_actors'])){ echo $lang_index_movie_actors_title." ".$row['media_actors']; } else { echo $lang_media_recommended_no_info; } ?></div>
															<div class="recom-movies-details">
																<div><span><i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;color:#056899;">thumb_up</i> <?php echo $row['media_likes'] ?></span></div>
																<div><span><i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;color:#11915a;">remove_red_eye</i> <?php echo $row['media_views'] ?></span></div>
																<div><span><?php echo $rmovie_genres ?></span></div>
															</div>
														</div>
														<?php
													}
												}
												catch(PDOException $e) {
													echo "MySQL connection failed: " . $e->getMessage();
												}
												?>
											</div>
											<?php include 'plugins/after_media.php'; ?>
										</div>
									</div>
								</div>
							</main>
						</div>
						<?php if(suggest_dialog_check($conf_movie_suggest_logged_only)){ ?>
							<dialog class="mdl-dialog dialog-style suggest-movie-dialog" id="dialog-1">
								<div class="dialog-header dgheader1"><span style="font-weight:bold;color:#fefefe;">YÊU CẦU</span><br><span style="color:#ebd5d5;">Cảm ơn sự đóng góp của bạn</span></div>
								<form id="postSuggestionFormId" method="post" name="SuggestForm" onsubmit="return validateSuggestForm()" action="system/functions/post_suggestion.php">
									<div class="mdl-dialog__content" style="color:#eeeeee !important;padding:20px 50px 20px !important;">
										<div id="suggestBoxMessage"></div>
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;">
											<input class="mdl-textfield__input" type="text" name="answer_a" id="sg_a" style="border-bottom:1px solid #3b3b3b !important;">
											<label class="mdl-textfield__label" for="sg_a" style="color:#b7b4b4;">Tên bộ phim...</label>
										</div>
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;">
											<input class="mdl-textfield__input" type="email" name="answer_b" id="sg_b" style="border-bottom:1px solid #3b3b3b !important;">
											<label class="mdl-textfield__label" for="sg_b" style="color:#b7b4b4;">Email...</label>
										</div>
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;margin-bottom:10px;">
											<textarea class="mdl-textfield__input" type="text" name="answer_c" id="sg_c" style="border-bottom:1px solid #3b3b3b !important;min-height:120px;"></textarea>
											<label class="mdl-textfield__label" for="sg_c" style="color:#b7b4b4;">Thông tin thêm...</label>
										</div>
										<span style="color:#f26a6a;">Trong \"Thông tin thêm\" vui lòng xác định loại phim (Ví dụ: Hoạt hình, Series,...) và thông tin quan trọng khác liên quan đến bộ phim bạn yêu cầu. Biểu mẫu này chỉ dành cho yêu cầu phim, nếu bạn có câu hỏi khác, vui lòng liên hệ với chúng tôi bằng biểu mẫu liên hệ.</span>
									</div>
									<div class="mdl-dialog__actions" style="padding:20px 50px 20px !important;">
										<button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" style="background-color:#036a79 !important;">Gửi</button>
										<button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent close-dialog-1" style="background-color:#343434 !important;">Đóng</button>
									</div>
								</form>
							</dialog>
						<?php } if(!isset($_SESSION['username'])){ ?>
							<dialog class="mdl-dialog dialog-style login-dialog" id="dialog-2">
								<div class="dialog-header dgheader2"><span style="font-weight:bold;color:#fefefe;">ĐĂNG NHẬP</span><br><span style="color:#e4ffff;">Đăng nhập vào tài khoản của bạn</span></div>
								<form method="post" name="LoginForm" onsubmit="return validateLoginForm()" action="system/login/checklogin.php">
									<div class="mdl-dialog__content" style="color:#eeeeee !important;padding:20px 50px 20px !important;">
										<div id="loginMessage"></div>
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;">
											<input class="mdl-textfield__input" type="text" name="myusername" id="myusername" style="border-bottom:1px solid #3b3b3b !important;">
											<label class="mdl-textfield__label" for="myusername" style="color:#b7b4b4;">Tài khoản...</label>
										</div>
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;margin-bottom:15px;">
											<input class="mdl-textfield__input" type="password" name="mypassword" id="mypassword" style="border-bottom:1px solid #3b3b3b !important;">
											<label class="mdl-textfield__label" for="mypassword" style="color:#b7b4b4;">Mật khẩu...</label>
										</div>
										<span onclick="dialog2.close()" id="show-register-dialog" style="color:#d6d6d6;text-decoration:underline;cursor:pointer;">Tạo tài khoản</span>
										<span style="color:#4a4a4a;margin:0px 5px 0px 5px;">|</span>
										<span onclick="dialog2.close()" id="show-forgotpass-dialog" style="color:#d6d6d6;text-decoration:underline;cursor:pointer;">Quên mật khẩu?</span>
									</div>
									<div class="mdl-dialog__actions" style="padding:20px 50px 20px !important;">
										<button type="submit" id="loginSubmitBtn" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" style="background-color:#036a79 !important;">Đăng nhập</button>
										<button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent close-dialog-2" style="background-color:#343434 !important;">Đóng</button>
									</div>
								</form>
							</dialog>
							<dialog class="mdl-dialog dialog-style login-dialog" id="dialog-3">
								<div class="dialog-header dgheader2"><span style="font-weight:bold;color:#fefefe;">TẠO TÀI KHOẢN</span><br><span style="color:#e4ffff;">Tạo tài khoản mới</span></div>
								<form method="post" name="RegisterForm" id="usersignup" onsubmit="return validateRegisterForm()" action="system/login/createuser.php">
									<div class="mdl-dialog__content" style="color:#eeeeee !important;padding:20px 50px 20px !important;">
										<div id="registerMessage"></div>
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;">
											<input class="mdl-textfield__input" type="text" name="newuser" id="newuser" style="border-bottom:1px solid #3b3b3b !important;">
											<label class="mdl-textfield__label" for="newuser" style="color:#b7b4b4;">Tài khoản...</label>
										</div>
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;">
											<input class="mdl-textfield__input" type="email" name="email" id="email" style="border-bottom:1px solid #3b3b3b !important;">
											<label class="mdl-textfield__label" for="email" style="color:#b7b4b4;">Email...</label>
										</div>
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;">
											<input class="mdl-textfield__input" type="password" name="password1" id="password1" style="border-bottom:1px solid #3b3b3b !important;">
											<label class="mdl-textfield__label" for="password1" style="color:#b7b4b4;">Mật khẩu...</label>
										</div>
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;">
											<input class="mdl-textfield__input" type="password" name="password2" id="password2" style="border-bottom:1px solid #3b3b3b !important;">
											<label class="mdl-textfield__label" for="password2" style="color:#b7b4b4;">Nhập lại mật khẩu...</label>
										</div>
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;margin-bottom:15px;">
											<input class="mdl-textfield__input" type="number" name="answer_e" id="rg_e" style="border-bottom:1px solid #3b3b3b !important;">
											<label id="registerValidatorText" class="mdl-textfield__label" for="rg_e" style="color:#e74c3c;">Error</label>
										</div>
										<span onclick="dialog3.close();dialog2.showModal();" style="color:#d6d6d6;text-decoration:underline;cursor:pointer;">Đã có tài khoản? Đăng nhập.</span>
									</div>
									<div class="mdl-dialog__actions" style="padding:20px 50px 20px !important;">
										<button type="submit" id="regSubmitBtn" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" style="background-color:#036a79 !important;">Tạo tài khoản</button>
										<button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent close-dialog-3" style="background-color:#343434 !important;">Đóng</button>
									</div>
								</form>
							</dialog>
							<dialog class="mdl-dialog dialog-style login-dialog" id="dialog-4">
								<div class="dialog-header dgheader2"><span style="font-weight:bold;color:#fefefe;">KHÔI PHỤC MẬT KHẨU</span><br><span style="color:#e4ffff;">Gửi mật khẩu mới vào email của bạn</span></div>
								<form id="resetPasswordFormId" method="post" name="ForgotPassForm" onsubmit="return validateForgotPassForm()" action="system/functions/reset_password.php">
									<div class="mdl-dialog__content" style="color:#eeeeee !important;padding:20px 50px 20px !important;">
										<div id="resetPasswordMessage"></div>
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;margin-bottom:15px;">
											<input class="mdl-textfield__input" type="email" name="answer_a" id="fgp_a" style="border-bottom:1px solid #3b3b3b !important;">
											<label class="mdl-textfield__label" for="fgp_a" style="color:#b7b4b4;">Email...</label>
										</div>
										<span onclick="dialog4.close();dialog2.showModal();" style="color:#d6d6d6;text-decoration:underline;cursor:pointer;">Quay lại đăng nhập</span>
										<span style="color:#4a4a4a;margin:0px 5px 0px 5px;">|</span>
										<span onclick="dialog4.close();showRegisterModal();" style="color:#d6d6d6;text-decoration:underline;cursor:pointer;">Tạo tài khoản</span>
									</div>
									<div class="mdl-dialog__actions" style="padding:20px 50px 20px !important;">
										<button id="resetPasswordSubmitBtn" type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" style="background-color:#036a79 !important;">Khôi phục mật khẩu</button>
										<button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent close-dialog-4" style="background-color:#343434 !important;">Đóng</button>
									</div>
								</form>
							</dialog>
						<?php } ?>
						<dialog class="mdl-dialog dialog-style information-dialog" id="dialog-5">
							<div class="dialog-header dgheader1"><span style="font-weight:bold;color:#fefefe;">ĐIỀU KHOẢN DỊCH VỤ</span><br><span style="color:#ebd5d5;">Điều khoản và điều kiện chung của trang web này</span></div>
							<div class="mdl-dialog__content" style="color:#eeeeee !important;padding:20px 50px 20px !important;">
								<?php
								try {
									$stmt = $conn->prepare("SELECT * FROM ".$table_pages." WHERE `identify`='1'");
									$stmt->execute();
									$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
									foreach($stmt->fetchAll() as $row){
										echo $row['rules_page'];
									}
								} catch(PDOException $e) {
									echo $e->getMessage();
								}
								?>
							</div>
							<div class="mdl-dialog__actions" style="padding:20px 50px 20px !important;">
								<button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent close-dialog-5" style="background-color:#343434 !important;">Đóng</button>
							</div>
						</dialog>
						<dialog class="mdl-dialog dialog-style information-dialog" id="dialog-6">
							<div class="dialog-header dgheader1"><span style="font-weight:bold;color:#fefefe;">CHÍNH SÁCH BẢO MẬT</span><br><span style="color:#ebd5d5;">Thông tin về cookie và thu thập dữ liệu</span></div>
							<div class="mdl-dialog__content" style="color:#eeeeee !important;padding:20px 50px 20px !important;">
								<?php
								try {
									$stmt = $conn->prepare("SELECT * FROM ".$table_pages." WHERE `identify`='1'");
									$stmt->execute();
									$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
									foreach($stmt->fetchAll() as $row){
										echo $row['policy_page'];
									}
								} catch(PDOException $e) {
									echo $e->getMessage();
								}
								?>
							</div>
							<div class="mdl-dialog__actions" style="padding:20px 50px 20px !important;">
								<button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent close-dialog-6" style="background-color:#343434 !important;">Đóng</button>
							</div>
						</dialog>
						<dialog class="mdl-dialog dialog-style information-dialog" id="dialog-7">
							<div class="dialog-header dgheader1"><span style="font-weight:bold;color:#fefefe;">VỀ CHÚNG TÔI</span><br><span style="color:#ebd5d5;">Thông tin về trang web của chúng tôi</span></div>
							<div class="mdl-dialog__content" style="color:#eeeeee !important;padding:20px 50px 20px !important;">
								<?php
								try {
									$stmt = $conn->prepare("SELECT * FROM ".$table_pages." WHERE `identify`='1'");
									$stmt->execute();
									$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
									foreach($stmt->fetchAll() as $row){
										echo $row['about_page'];
									}
								} catch(PDOException $e) {
									echo $e->getMessage();
								}
								?>
							</div>
							<div class="mdl-dialog__actions" style="padding:20px 50px 20px !important;">
								<button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent close-dialog-7" style="background-color:#343434 !important;">Đóng</button>
							</div>
						</dialog>
						<dialog class="mdl-dialog dialog-style suggest-movie-dialog" id="dialog-8">
							<div class="dialog-header dgheader1"><span style="font-weight:bold;color:#fefefe;">LIÊN HỆ</span><br><span style="color:#ebd5d5;">Có một vài câu hỏi? Gửi cho chúng tôi câu hỏi của bạn!</span></div>
							<form id="postContactFormId" method="post" name="ContactForm" onsubmit="return validateContactForm()" action="system/functions/post_contact.php">
								<div class="mdl-dialog__content" style="color:#eeeeee !important;padding:20px 50px 20px !important;">
									<div id="contactBoxMessage"></div>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;">
										<input class="mdl-textfield__input" type="text" name="answer_a" id="cg_a" style="border-bottom:1px solid #3b3b3b !important;">
										<label class="mdl-textfield__label" for="cg_a" style="color:#b7b4b4;">Chủ đề...</label>
									</div>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;">
										<input class="mdl-textfield__input" type="text" name="answer_b" id="cg_b" style="border-bottom:1px solid #3b3b3b !important;">
										<label class="mdl-textfield__label" for="cg_b" style="color:#b7b4b4;">Tên...</label>
									</div>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;">
										<input class="mdl-textfield__input" type="email" name="answer_c" id="cg_c" style="border-bottom:1px solid #3b3b3b !important;">
										<label class="mdl-textfield__label" for="cg_c" style="color:#b7b4b4;">Email...</label>
									</div>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;margin-bottom:10px;">
										<textarea class="mdl-textfield__input" type="text" name="answer_d" id="cg_d" style="border-bottom:1px solid #3b3b3b !important;min-height:120px;"></textarea>
										<label class="mdl-textfield__label" for="cg_d" style="color:#b7b4b4;">Tin nhắn...</label>
									</div>
									<span style="color:#f26a6a;">Sử dụng mẫu này nếu bạn có bất kỳ câu hỏi nào về trang web này hoặc tìm thấy một vài lỗi. Hãy viết câu hỏi của bạn rõ ràng và chắc chắn. Chúng tôi sẽ từ chối tất cả các câu hỏi không liên quan đến trang web này. Nếu bạn muốn yêu cầu một bộ phim, vui lòng sử dụng mẫu Yêu cầu phim..</span>
								</div>
								<div class="mdl-dialog__actions" style="padding:20px 50px 20px !important;">
									<button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" style="background-color:#036a79 !important;">Gửi</button>
									<button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent close-dialog-8" style="background-color:#343434 !important;">Đóng</button>
								</div>
							</form>
						</dialog>
						<?php if(isset($_SESSION['username'])){ ?>
							<dialog class="mdl-dialog dialog-style suggest-movie-dialog" id="dialog-9">
								<div class="dialog-header dgheader1"><span style="font-weight:bold;color:#fefefe;">TÀI KHOẢN CỦA TÔI</span><br><span style="color:#ebd5d5;">Thông tin tài khoản và cài đặt</span></div>
								<form id="updateAccountFormId" method="post" name="AccountUpdateForm" onsubmit="return validateAccountUpdateForm()" action="system/functions/update_account.php">
									<div class="profile-topbox">
										<div id="accountAvatarImage" onclick="chooseAvatarFile()" class="profile-image-box" style="background: url('<?php echo $_SESSION['avatar_img'] ?>') center / cover;"></div>
										<div class="mdl-tooltip mdl-tooltip--large" data-mdl-for="accountAvatarImage">Thay đổi hình đại diện</div>
										<div class="profile-detail-box">
											<div style="font-size:18px;padding-top:52px;color:#e1e1e1;"><?php echo $_SESSION['username'] ?></div>
											<div style="font-size:14px;padding-top:5px;color:#c0c0c0;"><?php if($crypt->decrypt($_SESSION['is_admin'],$secret_key,$secret_iv) == '1'){ echo $lang_dialog_my_account_user_type_admin; } else { echo $lang_dialog_my_account_user_type_normal; } ?></div>
										</div>
									</div>
									<div class="mdl-dialog__content" style="color:#eeeeee !important;padding:20px 50px 20px !important;">
										<div id="accountUpdateMessage"></div>
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;">
											<input class="mdl-textfield__input" type="email" value="<?php echo $_SESSION['my_email'] ?>" name="answer_a" id="acc_a" style="border-bottom:1px solid #3b3b3b !important;">
											<label class="mdl-textfield__label" for="acc_a" style="color:#b7b4b4;">Email...</label>
										</div>
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;">
											<input class="mdl-textfield__input" type="password" name="answer_b" id="acc_b" style="border-bottom:1px solid #3b3b3b !important;">
											<label class="mdl-textfield__label" for="acc_b" style="color:#b7b4b4;">Mật khẩu hiện tại...</label>
										</div>
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;">
											<input class="mdl-textfield__input" type="password" name="answer_c" id="acc_c" style="border-bottom:1px solid #3b3b3b !important;">
											<label class="mdl-textfield__label" for="acc_c" style="color:#b7b4b4;">Mật khẩu mới...</label>
										</div>
										<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;margin-bottom:10px;">
											<input class="mdl-textfield__input" type="password" name="answer_d" id="acc_d" style="border-bottom:1px solid #3b3b3b !important;">
											<label class="mdl-textfield__label" for="acc_d" style="color:#b7b4b4;">Nhập lại mật khẩu mới...</label>
										</div>
										<input type="hidden" id="avatarUrlInput" name="answer_e" value="<?php echo $_SESSION['avatar_img'] ?>">
										<span style="color:#f26a6a;"><?php echo $lang_dialog_my_account_warn ?></span>
									</div>
									<div class="mdl-dialog__actions" style="padding:20px 50px 20px !important;">
										<button id="accountUpdateSubmitBtn" type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" style="background-color:#036a79 !important;">Cập nhật cài đặt</button>
										<button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent close-dialog-9" style="background-color:#343434 !important;">Đóng</button>
									</div>
								</form>
							</dialog>
							<!--Xem Phim-->
						<?php } ?>
						<dialog class="mdl-dialog dialog-style watch-movie-dialog" id="dialog-10">
							<div class="watch-movie-header">
								<span style="color:#aec0c7;">
									<i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;">play_circle_outline</i> Movie player 
									<i class="material-icons close-dialog-10" style="float:right;margin:-2px 15px 0px 0px;color:#d7e0e3;cursor:pointer;">clear</i>
								</span>
							</div>
							<div class="watch-movie-content">
								<?php $movie_series = explode("[div]",$grow['media_series']); ?>
								<div <?php if(sizeof($movie_series) <= 1 && $conf_movie_always_show_series_bar != true){ echo 'style="display:none !important;"'; } ?> class="watch-series" data-simplebar>
									<?php
									$serie_num = 0;
									foreach($movie_series as $serie){ $serie_num++; ?>
										<button class="series-link" onclick="openPlayer('serie-<?php echo $serie_num ?>', this, '#249af2')" <?php if($serie_num == 1){ echo 'id="defaultPlay"'; } ?>><?php echo str_replace("%a",$serie_num,$lang_media_watch_dialog_serie_name) ?>
									</button>
								<?php } ?>
							</div>
							<?php $wserie_num = 0;
							foreach($movie_series as $serie){ $wserie_num++; ?>
								<div id="serie-<?php echo $wserie_num ?>" class="watch-player watch-video-content">
									<?php if(isYoutube($serie)){ ?>
										<div id="video-player-serie-<?php echo $wserie_num ?>" data-plyr-provider="youtube" data-plyr-embed-id="<?php echo getVideoId($serie) ?>" <?php if($conf_watch_media_ads && $conf_vi_ai_publisher_id != ""){echo 'data-plyr-config=\'{ "ads": { enabled: true, publisherId: \''.$conf_vi_ai_publisher_id.'\' } }\'';} ?>></div>
									<?php } elseif(isVimeo($serie)){ ?>
										<div id="video-player-serie-<?php echo $wserie_num ?>" data-plyr-provider="vimeo" data-plyr-embed-id="<?php echo getVideoId($serie) ?>" <?php if($conf_watch_media_ads && $conf_vi_ai_publisher_id != ""){echo 'data-plyr-config=\'{ "ads": { enabled: true, publisherId: \''.$conf_vi_ai_publisher_id.'\' } }\'';} ?>></div>
									<?php } elseif(isHls($serie)){ ?>
										<input type="hidden" id="hls-source-serie-<?php echo $wserie_num ?>" value="<?php echo $serie ?>">
										<video id="hls-player-serie-<?php echo $wserie_num ?>" controls crossorigin playsinline <?php if($conf_use_custom_poster){echo 'poster="'.$video_poster_image_url.'"';} ?> <?php if($conf_watch_media_ads && $conf_vi_ai_publisher_id != ""){echo 'data-plyr-config=\'{ "ads": { enabled: true, publisherId: \''.$conf_vi_ai_publisher_id.'\' } }\'';} ?>>
											<div class="login-req-message-warn"><div style="color:#f3ebd7;font-size:16px;padding-top:15px;padding-left:15px;margin-top:20px;"><i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;">warning</i> <?php echo $lang_media_video_player_error ?></div></div>
										</video>
									<?php } elseif(isHtml($serie)){ $parsed = get_string_between($serie, "[html]", "[/html]"); ?>
									<div id="html-player-serie-<?php echo $wserie_num ?>" class="html-watch-box"><?php echo $parsed ?></div>
								<?php } 
								else { ?>
									<video id="video-player-serie-<?php echo $wserie_num ?>" controls crossorigin playsinline <?php 
									if($conf_use_custom_poster){echo 'poster="'.$video_poster_image_url.'"';} ?> 
									<?php 
									if($conf_watch_media_ads && $conf_vi_ai_publisher_id != ""){echo 'data-plyr-config=\'{ "ads": { enabled: true, publisherId: \''.$conf_vi_ai_publisher_id.'\' } }\'';} ?>>
									<source src="<?php echo $serie ?>" type="video/mp4">
										<div class="login-req-message-warn">
											<div style="color:#f3ebd7;font-size:16px;padding-top:15px;padding-left:15px;margin-top:20px;">
												<i class="material-icons" style="float:left;margin:-2px 10px 0px 0px;">warning</i>Trình duyệt của bạn không hỗ trợ video HTML5, vui lòng cập nhật trình duyệt.
											</div>
										</div>
									</video>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				</dialog>
				<dialog class="mdl-dialog dialog-style suggest-movie-dialog" id="dialog-11">
					<div class="dialog-header dgheader1"><span style="font-weight:bold;color:#fefefe;">BÁO CÁO</span><br><span style="color:#ebd5d5;"><?php echo $lang_media_dialog_report_subtitle ?></span></div>
					<form id="postReportFormId" method="post" name="ReportForm" onsubmit="return validateReportForm()" action="system/functions/post_report.php">
						<div class="mdl-dialog__content" style="color:#eeeeee !important;padding:20px 50px 20px !important;">
							<div id="reportBoxMessage"></div>
							<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;">
								<input class="mdl-textfield__input" type="text" value="<?php echo $grow['media_name'] ?>" name="answer_a" id="rp_a" style="border-bottom:1px solid #3b3b3b !important;">
								<label class="mdl-textfield__label" for="rp_a" style="color:#b7b4b4;">Tên bộ phim...</label>
							</div>
							<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;">
								<input class="mdl-textfield__input" type="email" name="answer_b" id="rp_b" style="border-bottom:1px solid #3b3b3b !important;">
								<label class="mdl-textfield__label" for="rp_b" style="color:#b7b4b4;">Email...</label>
							</div>
							<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100% !important;margin-bottom:10px;">
								<textarea class="mdl-textfield__input" type="text" name="answer_c" id="rp_c" style="border-bottom:1px solid #3b3b3b !important;min-height:120px;"></textarea>
								<label class="mdl-textfield__label" for="rp_c" style="color:#b7b4b4;">Tin nhắn...</label>
							</div>
							<span style="color:#f26a6a;">Sử dụng mẫu này nếu: 1. Có vấn đề khi phát phim này hoặc thông tin không chính xác. 2. Bạn là tác giả của nội dung này và muốn xóa nội dung này. Nếu bạn có câu hỏi khác, hãy sử dụng mẫu Liên hệ.</span>
						</div>
						<div class="mdl-dialog__actions" style="padding:20px 50px 20px !important;">
							<button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" style="background-color:#036a79 !important;">Gửi</button>
							<button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent close-dialog-11" style="background-color:#343434 !important;">Đóng</button>
						</div>
					</form>
				</dialog>
				<input type="hidden" id="lang_dialog_empty_field_error" value="Tất cả các phần phải được điền đầy đủ!">
				<input type="hidden" id="lang_global_loading_text" value="Xin vui lòng chờ...">
				<input type="hidden" id="lang_dialog_register_captcha_error" value="Câu trả lời bảo mật không hợp lệ!">
				<input type="hidden" id="lang_dialog_pass_too_short_error" value="Mật khẩu phải dài ít nhất 4 ký tự!">
				<input type="hidden" id="lang_dialog_my_account_password_mismatch_error" value="Mật khẩu không hợp lệ!">
				<input type="hidden" id="lang_toast_suggest_only_logged" value="Chỉ người dùng đã đăng nhập mới có thể yêu cầu phim.">
				<input type="hidden" id="lang_popup_avatar_image_prompt" value="Nhập url hình ảnh đại diện ở đây">
				<input type="hidden" id="lang_popup_avatar_image_error" value="Url hình ảnh không hợp lệ hoặc định dạng không được hỗ trợ.">
				<input type="hidden" id="lang_dialog_my_account_done_msg" value="Cập nhật cài đặt tài khoản!">
				<input type="hidden" id="lang_dialog_forgot_pass_done_msg" value="Mật khẩu mới được gửi đến email của bạn.">
				<input type="hidden" id="lang_toast_report_only_logged" value="Chỉ người dùng đã đăng nhập mới có thể gửi báo cáo.">
				<input type="hidden" id="lang_media_coverside_favorite_button" value="Thêm vào mục yêu thích">
				<input type="hidden" id="lang_media_coverside_favorite_remove" value="Loại bỏ khỏi mục ưa thích">
				<input type="hidden" id="lang_like_button_done_text" value="Đã thích! +1">
				<input type="hidden" id="lang_review_timeout_msg" value="Bạn phải đợi trước khi đăng bài đánh giá khác.">
				<input type="hidden" id="lang_media_reviews_field_empty_error" value="Mục đánh giá không thể để trống.">
				<div id="bottom-toast" class="mdl-js-snackbar mdl-snackbar">
					<div class="mdl-snackbar__text"></div>
					<button class="mdl-snackbar__action" type="button"></button>
				</div>
				<script src="styles/dialog-polyfill.js"></script>
				<script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=es6,Array.prototype.includes,CustomEvent,Object.entries,Object.values,URL"></script>
				<script src="plyr/plyr.min.js"></script>
				<script src="scripts/functions.js"></script>
				<script src="scripts/hls.min.js"></script>
				<script src="scripts/dialog_boxes.js"></script>
				<?php include 'plugins/body_bottom.php'; ?>
				<script>document.getElementById("defaultOpen").click();</script>

				<script type="text/javascript">
					$(document).ready(function(){
					});
					$(document).ready(function(){
						$("#exit").click(function(){
							var exit = confirm("Bạn có muốn thoát không?");
							if(exit==true){window.location = 'index.php?logout=true';}     
						});
					});
					$("#submitmsg").click(function(){
						var clientmsg = $("#usermsg").val();
						$.post("post.php", {text: clientmsg});             
						$("#usermsg").attr("value", "");
						loadLog;
						return false;
					});
					function loadLog(){    
						var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
						$.ajax({
							url: "log.html",
							cache: false,
							success: function(html){       
								$("#chatbox").html(html);       
								var newscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
								if(newscrollHeight > oldscrollHeight){
									$("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal');
								}              
							},
						});
					}
					setInterval (loadLog, 2500);
				</script>

			</body>
			</html>
			<?php
		}
	} else {
		include 'system/no_media_found.php';
	}
}
catch(PDOException $ge) {
	echo "MySQL connection failed: " . $ge->getMessage();
}
} else {
	include 'system/no_media_found.php';
}
?>