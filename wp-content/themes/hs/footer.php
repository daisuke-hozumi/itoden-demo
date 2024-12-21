<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package hs
 */
?>
<!-- 上部へ戻る -->
<a href="#" id="page_top"><img class="page_top_img" src="<?php echo(get_template_directory_uri())?>/img/page_top.svg" alt="ページ上部へ"></a>

<style>
    footer {
        margin-top: 50px;
    }
    footer .inner {
        background-color: #F3F3F3;
        padding-top: 40px;
        padding-bottom: 35px;
        text-align: center;
    }
    footer .inner * {
        color: #AAAAAA;
    }
    footer a img {
        max-width: 464px;
        margin-bottom: 15px;
    }
    footer p {
        font-size: 19px;
        margin-bottom: 20px;
    }
    footer small {
        margin-top: 20px;
        font-size: 16px;
    }
</style>
<footer>
    <div class="inner">
        <a href="/"><img src="/img/logo.png" alt="株式会社イトデンエンジニアリング"></a>
        <p>太陽光発電 料金・発電量シミュレーションサイト</p>
        <small>Copyright &copy; ITODEN ENGINEERING Co.Ltd. All Rights Reserved.</small>
    </div>
</footer>