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
<a href="#" id="page_top"><img class="page_top_img" src="<?php echo(get_template_directory_uri())?>/img/icon_top.svg" alt="ページ上部へ"></a>

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
        <a href="/"><img src="/img/logo.png" style="max-width: 90%;" alt="株式会社イトデンエンジニアリング"></a>
        <p>太陽光発電 料金・発電量<br class="disp_tb_miman">シミュレーションサイト</p>
        <small>Copyright &copy; ITODEN ENGINEERING Co.Ltd. <br class="disp_tb_miman">All Rights Reserved.</small>
    </div>
</footer>
</body>
<script src="<?php echo(get_template_directory_uri()) ?>/js/jquery-3.4.1.min.js"></script>
<!-- スクロール関連処理 -->
<script src="<?php echo(get_template_directory_uri()) ?>/js/jsScroll.js"></script>
<?php if (is_page('simulation')) { ?>
<script src="<?php echo(get_template_directory_uri()) ?>/js/jsCheck.js"></script>
<?php } ?>
<!-- スクロールヒント -->
<link rel="stylesheet" href="https://unpkg.com/scroll-hint@latest/css/scroll-hint.css">
<script src="https://unpkg.com/scroll-hint@latest/js/scroll-hint.min.js"></script>
<script>
  window.addEventListener('DOMContentLoaded', function () {
    new ScrollHint('.js-scrollable', {
      scrollHintIconAppendClass: 'scroll-hint-icon-white', 
      suggestiveShadow: true,
      i18n: {
        scrollable: "スクロールできます"
      }
    });
  });
</script>
</html>