<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package hs
 */

//---------------------------------------
// ヘッダーの読み込み
//---------------------------------------
get_header();
?>

<main>
        <section class="mv">
            <img src="/img/mv.png" alt="" width="100%" height="auto">
            <style>
                .mv .subcatch {
                    background-color: #1E3A6E;
                    padding: 1em;
                    text-align: center;
                    color: #fff;
                    font-size: 20px;
                    font-weight: 600;
                }
            </style>
            <div class="subcatch">
                <h2>株式会社 イトデン エンジニアリングが運営する、太陽光発電に関するシミュレーションサービス</h2>
            </div>
        </section>
        
        <style>
            * {
                color: #3A3A3A;
                font-family: YuGothic, "Yu Gothic medium", "Hiragino Sans", Meiryo, "sans-serif"
            }
            .mv .subcatch {
                background-color: #1E3A6E;
                padding: 1em;
                text-align: center;
                color: #fff;
                font-size: 20px;
                font-weight: 600;
            }
            .about {
                margin-bottom: 40px;
            }
            .about .inner {
                background-color: #FFFFFF;
                padding-top: 50px;
                padding-bottom: 50px;
            }
            .about .cont {
                display: flex;
                justify-content: space-between
            }
            .about .cont .left {
                width: 55%;
            }
            .about .cont .left ul {
                font-size: 22px;
                margin-bottom: 1em;
            }
            .about .cont .left li {
                color: #1FA2C3;
                font-weight: 600;
            }
            .about .cont .left li::before {
                content: '●';
                margin-right: 0.5em;
            }
            .about .cont .right {
                width: 37%;
            }
            .about .cont .right img {
                max-width: 100%;
            }
            .about h3 {
                font-size: 30px;
                color: #1E3A6E;
                font-weight: 600;
                position: relative;
                margin-bottom: 1em;
            }
            .about h3::after {
                content: '';
                display: block;
                width: 30%;
                height: 2px;
                background-color: #1E3A6E;
                margin-top: .5em;
            }
            .services {
                margin-bottom: 40px;
            }
            .services h3 {
                background-color: #1FA2C3;
                color: #fff;
                padding: 0.3em 1em;
                font-size: 30px;
                font-weight: 600;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
            }
            .services .text {
                padding: 1.5em;
                font-size: 19px;
                box-shadow: 0px 5px 10px 0px rgba(0, 0, 0, 0.35);
            }
            .services .text p {
                color: #1FA2C3;
                margin-bottom: 1em;
            }
            .services .text li::before {
                content: '●';
                margin-right: 0.5em;
                color: #1FA2C3
            }
            .services .text li {
                font-size: 25px;
                font-weight: 600;
                border-bottom: 2px dashed #C8C8C8;
            }
            .run p {
                margin-bottom: 50px;
            }
        </style>
        <section class="about">
            <div class="inner">
                <div class="cont">
                    <div class="left">
                        <h3>
                            株式会社 イトデン エンジニアリングの<br>太陽光発電システムとは？
                        </h3>
                        <ul>
                            <li>産業用太陽光発電と蓄電システム</li>
                            <li>自家消費型太陽光発電システム</li>
                        </ul>
                        <p>
                            の導入および、自社グループの発電所で蓄積したノウハウを基に、太陽光発電所の運用　（オペレーション）と保守（メンテナンス）を行い、20年間の長期にわたり安定した電量を確保するためのサービスを提供します。
                        </p>
                    </div>
                    <div class="right">
                        <img src="/img/top_img.png" alt="太陽光発電イメージ">
                    </div>
                </div>
            </div>
        </section>
        <section class="services">
            <div class="inner">
                <div class="cont">
                    <h3>本サービスでわかること</h3>
                    <div class="text">
                        <p>簡単な条件を入力するだけで、当社の太陽光発電設備を導入した場合にが下記の内容が計算され、表示されます。</p>
                        <ul>
                            <li>導入コストがいくらかかるか</li>
                            <li>月間および年間にどれぐらいの電力を発電できるのか</li>
                            <li>年間でいくら削減できるのか</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <style>
            .run {
                text-align: center;
            }
            .run p {
                font-size: 24px;
                font-weight: 500;
                text-align: center;
            }
            .run img {
                width: 60%;
            }
        </style>
        <section class="run">
            <div class="cont">
                <p>
                    1分程度の簡単入力ですぐに結果が表示される<br>便利なツールとなっておりますので、ぜひご活用くださいませ。
                </p>
                <a href="/simulation/"><img src="/img/btn_run.png" alt="簡単１分！太陽光発電の料金シミュレーションを開始する"></a>
            </div>    
        </section>
    </main>
<?php
//---------------------------------------
// フッターの読み込み
//---------------------------------------
get_footer();