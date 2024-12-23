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
                <li itemprop="name"><a href="/simulation/" itemprop="url">シミュレーションスタート</a></li>
                <li itemprop="name"><a href="https://www.itoden-eng.co.jp/contact/form.cgi" itemprop="url">お問い合わせ</a></li>
            </ul>
        </nav>
        <!-- スマホメニューバー上のロゴ -->
        <a class="header_link_sp link_img" href="/"><img src="/img/logo.png" alt="株式会社イトデンエンジニアリング"></a>
        <!-- モーダル用Div -->
        <div class="modal"></div>
    </div>
</header>