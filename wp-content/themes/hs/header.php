<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package hs
 */

if( get_the_ID() === 2896 ) {
	$strH1 = '教育・医療・官公庁施設｜株式会社赤鹿建設 - 兵庫県姫路市の建設会社';
} elseif( get_the_ID() === 2857 ) {
	$strH1 = 'トップメッセージ｜株式会社赤鹿建設 - 兵庫県姫路市の建設会社';
} elseif( get_the_ID() === 2822 ) {
	$strH1 = '赤鹿建設について｜株式会社赤鹿建設 - 兵庫県姫路市の建設会社';
} elseif( get_the_ID() === 2825 ) {
	$strH1 = '会社概要｜株式会社赤鹿建設 - 兵庫県姫路市の建設会社';
} elseif( get_the_ID() === 2837 ) {
	$strH1 = '採用情報｜株式会社赤鹿建設 - 兵庫県姫路市の建設会社';
} elseif( get_the_ID() === 2835 ) {
	$strH1 = 'グループ会社｜株式会社赤鹿建設 - 兵庫県姫路市の建設会社';
} elseif( get_the_ID() === 2860 ) {
	$strH1 = '保護者様へ｜株式会社赤鹿建設 - 兵庫県姫路市の建設会社';
} elseif( get_the_ID() === 2900 ) {
	$strH1 = 'アカシカグループ物件について｜株式会社赤鹿建設 - 兵庫県姫路市の建設会社';
} elseif( get_the_ID() === 2898 ) {
	$strH1 = 'オフィス・生産・生活施設工事について｜株式会社赤鹿建設 - 兵庫県姫路市の建設会社';
} elseif( get_the_ID() === 2902 ) {
	$strH1 = '店舗・住宅・リニューアル工事他｜株式会社赤鹿建設 - 兵庫県姫路市の建設会社';
} elseif( strpos($_SERVER['REQUEST_URI'],'case') !== false || strpos($_SERVER['REQUEST_URI'],'category') !== false ) {
	if ( is_archive() ) {
		$cat = get_the_archive_title();
		if ( strpos($cat,'施工実績') === false ) { 
			$strH1 = $cat . ' 施工実績一覧｜株式会社赤鹿建設 - 兵庫県姫路市の建設会社';
		} else {
			$strH1 = '施工実績一覧｜株式会社赤鹿建設 - 兵庫県姫路市の建設会社';
		}
	} else {
		if ( is_single() ) {
			$strH1 = get_the_title() . '｜株式会社赤鹿建設の施工実績';
		}
	}
} elseif( strpos($_SERVER['REQUEST_URI'],'contact') !== false ) {
	$strH1 = 'お問い合わせ - 兵庫県姫路市の建設会社';
} elseif( strpos($_SERVER['REQUEST_URI'],'news') !== false ) {
	$strH1 = get_the_title() . '｜株式会社建設会社からのお知らせ';
} else {
	$strH1 = '株式会社赤鹿建設｜兵庫県姫路市の建設会社';	
}

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

    <!-- インラインで読み込むCSS -->
    <?php wp_head(); ?>
    <style>
    <?php include_once(get_template_directory() . "/css/critical-min.css"); ?>
    </style>	
	<link rel="stylesheet" href="<?php echo(get_template_directory_uri()) ?>/css/common.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="<?php echo(get_template_directory_uri()) ?>/css/style.css" media="print" onload="this.media='all'">
</head>

<body <?php body_class(); ?>>
	<style>
        /* エラー時の入力欄のスタイル */
        .error-border {
            border: 2px solid #E43F24;
        }
        .error-hidden {
            display: none;
        }
        /* エラーメッセージの初期状態（非表示） */
        .error-visible {
            display: block;
            color: #E43F24;
            font-size: 0.9em;
            margin-top: 4px;
            font-weight: 600;
        }
        .sec-input_box {
            box-shadow: 0px 5px 15px 0px rgba(0, 0, 0, 0.35);
            margin-bottom: 50px;
        }
        .sec-input_box h2 {
            background-color: #1FA2C3;
            color: #fff;
            padding-top: 15px;
            padding-bottom: 15px;
            font-size: 18px;
            text-align: center;
            font-weight: 600;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .sec-input_box h2 span {
            font-size: 1.5em;
            margin-right: 5px;
        }
        .sec-input_box .inner {
            padding: 35px;
            text-align: center;
        }
        .sec-input_box .inner .input_box {
            display: inline-block;
            background-color: #EEEEEE;
            padding: 8px 20px;
            margin-bottom: 30px;
        }
        .sec-input_box .inner .input_box table {
            width: auto;
        }
        .sec-input_box .inner .input_box table th {
            padding-right: 20px;
        }
        .sec-input_box .inner .input_box select {
            padding: 12px 15px;
        }
        .sec-input_box .inner .map_box {
            background-color: #E8F6F9;
            padding: 35px;
            max-width: 750px;
            margin-left: auto;
            margin-right: auto;
        }
        input[type="text"] {
            width: 4em;
            text-align: right;
            margin-right: 13px;
            padding-right: 7px;
            padding-top: 9px;
            padding-bottom: 9px;
        }
        .sec-input_box#box-4 .input_box {
            width: 100%;
        }
        .sec-input_box#box-4 .flex {
            justify-content: space-between;
        }
        .sec-input_box#box-4 .left {
            width: 45%;
            padding-left: 40px;
        }
        .sec-input_box#box-4 .left table {
            width: 100%;
        }
        .sec-input_box#box-4 .left th {
            width: 40%;
            text-align: center;
        }
        .sec-input_box#box-4 .left td {
            width: 60%;
            text-align: left;
        }
        .sec-input_box#box-4 .right {
            width: 49%;
        }
        .sec-input_box#box-4 .right img {
            width: 100%;
        }
        section.run {
            margin-top: 50px;
            text-align: center;
        }
        section.run p {
            font-size: 24px;
            font-weight: 500;
            text-align: center;
        }
        section.run img {
            width: 60%;
        }
        section.run button,section.run a {
            border: none;
            background-color: #1FA2C3;
            color: #fff;
            text-align: center;
            font-weight: 600;
            font-size: 26px;
            padding: .7em 1.5em;
            text-decoration: none;
            display: inline-block;
        }
        section.run a {
            background-color: #FEA035;
        }
    </style>
<style>
    @media screen and (min-width:1024px) {
        .header_pc {
            display: flex;
            justify-content: space-between
        }
        .header_pc .left {
            width: 45%;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            padding-left: 15px;
        }
        .header_pc .left img {
            width: 55%;
        }
        .header_pc .right {
            width: 55%;
            display: flex;
        }
        .header_pc .right nav {
            width: 70%;
        }
        .header_pc .right nav ul {
            display: flex;
            justify-content: space-between;
        }
        .header_pc .right nav ul li {
            width: 33.31%;
        }
        .header_pc .right nav ul li img {
            width: 100%;
        }
        .header_pc .right .tel_box {
            width: 30%;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            align-items: center;
            text-align: center;
        }
        .header_pc .right .tel_box p {
            font-size: 15px;
            font-weight: 600;
            color: #AAAAAA;
        }
    }
</style>
<header>
    <div class="header_pc disp_pc_over">
        <div class="left">
            <div class="box">
                <a href="/">
                    <img src="/img/logo.png" alt="株式会社イトデンエンジニアリング">
                </a>
                <h1>太陽光発電 料金・発電量シミュレーションサイト</h1>
            </div>
        </div>
        <div class="right">
            <nav>
                <ul>
                    <li><a href="/"><img src="/img/nav_home.png" alt="HOME"></a></li>
                    <li class="run"><a href="/simulation/"><img src="/img/nav_run.png" alt="シミュレーションスタート"></a></li>
                    <li><a href="https://www.itoden-eng.co.jp/contact/form.cgi"><img src="/img/nav_contact.png" alt="お問い合わせ"></a></li>
                </ul>
            </nav>
            <div class="tel_box">
                <div class="box">
                    <img src="/img/header_tel.png" alt=""><br>
                    <p>対応時間：00:00～00:00</p>
                </div>
            </div>
        </div>
    </div>
    <div id="header_sp" class="disp_pc_miman clearfix">
        <!-- メニューボタン -->
        <input type="checkbox" class="check" id="checked">
        <label class="menu-btn" for="checked">
            <span class="bar top"></span>
            <span class="bar middle"></span>
            <span class="bar bottom"></span>
            <span class="menu-btn__text">MENU</span>
        </label>
        <label class="close-menu" for="checked"></label>
        <!-- ナビゲーション -->
        <nav class="drawer-menu">
            <p>MENU</p>
            <ul itemscope="" itemtype="http://schema.org/SiteNavigationElement">
                <li itemprop="name"><a href="/" itemprop="url">HOME</a></li>
                <li itemprop="name"><a href="/talent/" itemprop="url">シミュレーションスタート</a></li>
                <li itemprop="name"><a href="/audition/" itemprop="url">お問い合わせ</a></li>
            </ul>
        </nav>
        <!-- スマホメニューバー上のロゴ -->
        <a class="header_link_sp link_img disp_pc_miman_bl" href="/"><img src="/img/header_logo.png" alt="株式会社ジェステント（GESTENT）｜宮城・仙台の芸能プロダクション"></a>
        <!-- スマホメニューバー電話アイコン -->
        <a class="sp_tel link_img disp_pc_miman_bl" href="tel:022-212-3016"><img src="/img/sp_icon_tel.png" alt="電話でのお問い合わせ"></a>
        <!-- スマホメニューバーメールアイコン -->
        <a class="sp_mail link_img disp_pc_miman_bl" href="/contact/"><img src="/img/sp_icon_mail.png" alt="メールのでのお問い合わせ"></a>
        <!-- モーダル用Div -->
        <div class="modal"></div>
    </div>
</header>