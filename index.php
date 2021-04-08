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
<!doctype html>
<html>
<head>
	<link rel="shortcut icon" type="images/png" href="images/favicon.png"/>
	<title><?php echo $conf_site_title ?></title>
	<meta charset="UTF-8">
	<meta name="description" content="<?php echo str_replace('"','&quot;',$conf_meta_description) ?>">
	<meta name="keywords" content="<?php echo $conf_meta_keywords ?>">
	<meta name="theme-color" content="#1c1c1c">
	<meta name="viewport" content="width=device-width, initial-scale=0">
	<link rel="stylesheet" href="styles/material.min.css">
	<link rel="stylesheet" href="styles/custom.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="styles/simplebar.css" />
	<link rel="stylesheet" type="text/css" href="styles/dialog-polyfill.css" />
	<style id="dyn-style"></style>
	<script src="styles/material.min.js"></script>
	<script src="styles/simplebar.js"></script>
	<script src="scripts/jquery-3.3.1.min.js"></script>
	<script src="styles/shuffle.min.js"></script>
	<?php include 'plugins/head.php'; ?>
</head>
<body>
	<!--Facebook-->
	<!-- Load Facebook SDK for JavaScript -->
	<div id="fb-root"></div>
	<script>
		window.fbAsyncInit = function() {
			FB.init({
				xfbml            : true,
				version          : 'v9.0'
			});
		};

		(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = 'https://connect.facebook.net/vi_VN/sdk/xfbml.customerchat.js';
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>

		<!-- Your Chat Plugin code -->
		<div class="fb-customerchat"
		attribution=setup_tool
		page_id="106294368099646"
		theme_color="#ff5ca1"
		logged_in_greeting="Chào! Bạn cần trợ giúp gì ?"
		logged_out_greeting="Chào! Bạn cần trợ giúp gì ?">
	</div>
	<!--Facebook-->

	<!-- Tiêu đề cuộn Phim đề cử -->
	<div class="right-sidebar" data-simplebar>
		<div class="rs-header"><div style="padding-top:22px;">PHIM ĐỀ CỬ</div></div>
		<?php
	//Random Phim đề cử
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
		<!--Random Phim đề cử-->

		<div style="margin-bottom:28px;"></div>
	</div>
	<div id="scroll-body" class="mdl-layout mdl-js-layout" style="overflow-y:scroll !important;">
		<header class="mdl-layout__header mdl-layout__header--scroll" style="background-color: #1c1c1c;">
			<div class="mdl-layout__header-row">
				<!-- Title -->
				<a href="index.php"><img src="images/logophimhay.png" style="width:120px;"></a>
				<div class="header-buttons" id="header-buttons-id">
					<?php if(!isset($_SESSION['username'])){ ?>
						<button id="show-login-dialog" class="mdl-button mdl-js-button" style="color: #949494; font-weight: bold; margin-left: 50px;">
							<i class="material-icons" style="color:#1e4eb9;margin-right:5px;">account_circle</i>Đăng nhập
						</button>
						<?php 
					} else { ?>
						<button id="show-account-menu" class="mdl-button mdl-js-button" style="color: #e1e1e1; font-weight: bold; margin-left: 50px; height: 100% !important; border-radius: 0px !important; padding: 14px 16px !important;">
							<div id="menuAvatarImage" style="border-radius:50%;width:32px;height:32px;margin-right:15px;margin-top:2px;float:left;background:url('<?php echo $_SESSION['avatar_img'] ?>') center / cover;">
							</div>
							<?php echo $_SESSION['username'] ?>
							<i class="material-icons">keyboard_arrow_down</i>
						</button>
						<ul 
						class="mdl-menu mdl-menu--bottom-left mdl-js-menu mdl-js-ripple-effect" 
						style="min-width:250px;background-color:#191c21 !important;" 
						for="show-account-menu">
						<li id="show-myaccount-dialog" class="mdl-menu__item movie-category-li">
							<i class="material-icons account-menu-item">person</i>Tài khoản của tôi
						</li>
						<?php 
						if($crypt->decrypt($_SESSION['is_admin'],$secret_key,$secret_iv) == '1'){ ?>
							<li onclick="window.location.href='admin.php'" 
							class="mdl-menu__item movie-category-li">
							<i class="material-icons account-menu-item">settings</i>Bảng quản trị</li>
						<?php } 
						?>
						<li onclick="shuffleInstance.filter('my-watched')" 
						class="mdl-menu__item movie-category-li">
						<i class="material-icons account-menu-item">done</i>Xem phim
					</li>
					<li onclick="shuffleInstance.filter('my-favorite')" 
					class="mdl-menu__item movie-category-li">
					<i class="material-icons account-menu-item">favorite</i>Yêu thích
				</li>
				<li onclick="window.location.href='system/login/logout.php'" 
				class="mdl-menu__item movie-category-li">
				<i class="material-icons account-menu-item">exit_to_app</i>Thoát
			</li>
		</ul>
	<?php } ?>
	<button id="<?php 
	if(suggest_dialog_check($conf_movie_suggest_logged_only)){ echo 'show-suggest-dialog'; } 
	else { echo 'show-suggest-toast'; } ?>" 
	class="mdl-button mdl-js-button" 
	style="color: #949494; font-weight: bold; margin-left: 5px;">
	<i class="material-icons" style="color:#0cbb94;margin-right:5px;">note_add</i> <?php echo $lang_suggest_movie_button ?>
</button>
<button id="movie-categories-menu" class="mdl-button mdl-js-button" style="color: #949494; font-weight: bold; margin-left: 5px;">
	<i class="material-icons" style="color:#910d0d;margin-right:5px;">remove_red_eye</i> <?php echo $lang_movie_genres_button ?> 
	<i class="material-icons">keyboard_arrow_down</i>
</button>
<ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="movie-categories-menu" style="display:flex;background-color:#191c21 !important;padding:0 !important;width:750px !important;">
	<div class="movie-catg-col">
		<li onclick="shuffleInstance.filter('cat-comedy')" class="mdl-menu__item movie-category-li">Hài hước</li>
		<li onclick="shuffleInstance.filter('cat-documentary')" class="mdl-menu__item movie-category-li">Cổ trang</li>
		<li onclick="shuffleInstance.filter('cat-detective')" class="mdl-menu__item movie-category-li">Trinh thám</li>
		<li onclick="shuffleInstance.filter('cat-romantic')" class="mdl-menu__item movie-category-li">Lãng mạn</li>
	</div>
	<div class="movie-catg-col">
		<li onclick="shuffleInstance.filter('cat-adventure')" class="mdl-menu__item movie-category-li">Phiêu lưu</li>
		<li onclick="shuffleInstance.filter('cat-horror')" class="mdl-menu__item movie-category-li">Kinh dị</li>
		<li onclick="shuffleInstance.filter('cat-fantasy')" class="mdl-menu__item movie-category-li">Viễn tưởng</li>
		<li onclick="shuffleInstance.filter('cat-biography')" class="mdl-menu__item movie-category-li">Tiểu sử</li>
	</div>
	<div class="movie-catg-col">
		<li onclick="shuffleInstance.filter('cat-sport')" class="mdl-menu__item movie-category-li">Thể thao</li>
		<li onclick="shuffleInstance.filter('cat-action')" class="mdl-menu__item movie-category-li">Hành động</li>
		<li onclick="shuffleInstance.filter('cat-mystic')" class="mdl-menu__item movie-category-li">Huyền bí</li>
		<li onclick="shuffleInstance.filter('cat-war')" class="mdl-menu__item movie-category-li">Chiến tranh</li>
	</div>
	<div class="movie-catg-col">
		<li onclick="shuffleInstance.filter('cat-thriller')" class="mdl-menu__item movie-category-li">Ly kỳ</li>
		<li onclick="shuffleInstance.filter('cat-family')" class="mdl-menu__item movie-category-li">Gia đình</li>
		<li onclick="shuffleInstance.filter('cat-crime')" class="mdl-menu__item movie-category-li">Tội phạm</li>
		<li onclick="shuffleInstance.filter('cat-western')" class="mdl-menu__item movie-category-li">Miền Tây</li>
	</div>
	<div class="movie-catg-col mcc-right">
		<li onclick="shuffleInstance.filter('cat-music')" class="mdl-menu__item movie-category-li">Âm nhạc</li>
		<li onclick="shuffleInstance.filter('cat-history')" class="mdl-menu__item movie-category-li">Lịch sử</li>
		<li onclick="shuffleInstance.filter('cat-science')" class="mdl-menu__item movie-category-li">Khoa học</li>
		<li onclick="shuffleInstance.filter('cat-drama')" class="mdl-menu__item movie-category-li">Kịch</li>
	</div>
</ul>
</div>
<!--Tìm kiếm-->
<div class="header-buttons" id="header-search-id" style="width:50%;display:none;margin-left:50px;">
	<button class="mdl-button mdl-js-button" style="color:#e1e1e1;margin-top:15px;margin-right:15px;min-width:0 !important;padding:0 8px !important;"><i class="material-icons">search</i>
	</button>
	<div class="mdl-textfield mdl-js-textfield" style="width:100% !important;">
		<input onkeyup="handleSearchKeyup(event)" class="mdl-textfield__input" type="text" id="search_field" style="color:#eeeeee;">
		<label class="mdl-textfield__label" for="search_field" style="color:#b7b4b4;">Tìm kiếm phim và video...
		</label>
	</div>
</div>
<!-- Bộ đệm thanh điều hướng bên phải  -->
<div class="mdl-layout-spacer"></div>
<!-- Thanh điều hướng -->
<nav class="mdl-navigation">
	<span onclick="location.reload()" class="mdl-navigation__link" style="cursor:pointer;">
		<i class="material-icons">refresh</i>
	</span>
	<a class="mdl-navigation__link" href="index.php">
		<i class="material-icons">home</i>
	</a>
	<span onclick="switchSearchBox(true)" class="mdl-navigation__link" style="cursor:pointer;display:block;" id="show-search-btn">
		<i class="material-icons">search</i>
	</span>
	<span onclick="switchSearchBox(false)" class="mdl-navigation__link" style="cursor:pointer;display:none;" id="hide-search-btn">
		<i class="material-icons">clear</i>
	</span>
</nav>
