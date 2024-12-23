<?php
/**
 * The template for displaying all pages
 *
 * @package hs
 */

get_header();
?>
    <main>
        <section class="mv">
            <img src="/img/kasou_mv.png" alt="" width="100%" height="auto" class="disp_tb_over">
            <img src="/img/kasou_mv_sp.jpg" alt="" width="100%" height="auto" class="disp_tb_miman">
        </section>
        <form action="/simulation/result/" class="cont" method="post" name="frmInput" onsubmit="return funcInputCheck('frmInput')">
            <section class="input">
                <!-- 設置場所の地区選択 -->
                <section class="sec-input_box mt60">
                    <h2><span>01.</span>設置場所の地区、または、近い地区を選んでください。</h2>
                    <div class="inner">
                        <div class="input_box">
                            <table>
                                <th>設置場所の選択</th>
                                <td>
                                    <select name="cmbArea">
                                        <option value="">選択する</option>
                                        <option value="北海道">北海道</option>
                                        <option value="東北">東北</option>
                                        <option value="北陸">北陸</option>
                                        <option value="関東">関東</option>
                                        <option value="中部">中部</option>
                                        <option value="近畿">近畿</option>
                                        <option value="中国">中国</option>
                                        <option value="四国">四国</option>
                                        <option value="九州">九州</option>
                                        <option value="沖縄">沖縄</option>
                                    </select>
                                    <div class="error-hidden err_Area">設置場所を選択してください。</div>
                                </td>
                            </table>
                        </div>
                        <div class="map_box">
                            <img src="/img/japan_map.png" usemap="#ImageMap" alt="地図から地区を選択" id="japanmap"/>
                        </div>
                    </div>
                </section>

                <!-- 電力料金単価 -->
                <section class="sec-input_box">
                    <h2><span>02.</span>任意の電力量料金単価（税込み）から算出する。</h2>
                    <div class="inner">
                        <div class="in_bl t_left mb25">
                            <p>発電電力量（kWh）×入力した任意の電力量料金単価（円／kWh）にて、予想節約電気料金を単純算出します。<br>算出に用いる任意の電力量料金単価（税込み）を入力してください。</p>
                        </div>
                        <div class="input_box">
                            <table>
                                <th>電力料金単価</th>
                                <td>
                                    <input type="text" name="txtTanka"><span>円／kWh</span>
                                    <div class="error-hidden err_Tanka">電力料金単価を入力してください。</div>
                                </td>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- 設置場所の形状 -->
                <section class="sec-input_box">
                    <h2><span>03.</span>設置場所の形状を選択してください。</h2>
                    <div class="inner">
                        <div class="input_box">
                            <table>
                                <th>設置場所の選択</th>
                                <td>
                                    <select name="cmbPlace">
                                        <option value="">選択する</option>
                                        <?php
                                            $rack_pricing = get_field('rack_pricing','option');
                                            foreach($rack_pricing as $row) {
                                                echo('<option value="' . $row['roof_type'] . '">' . $row['roof_type'] . '</option>');
                                            }
                                        ?>
                                    </select>
                                    <div class="error-hidden err_Place">設置場所の形状を選択してください。</div>
                                </td>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- 設置場所情報の入力 -->
                <section class="sec-input_box" id="box-4">
                    <h2><span>04.</span>太陽光の設置場所情報を入力してください。</h2>
                    <div class="inner">
                        <div class="flex">
                            <div class="left">
                                <div class="input_box">
                                    <table>
                                        <th>設置面積</th>
                                        <td>
                                            <input type="text" name="txtMenseki" style="width: calc(100% - 1.6em); margin-right: 0;"><span style="margin-left: 5px;">㎡</span>
                                            <div class="error-hidden err_Menseki">設置面積を入力してください。</div>
                                        </td>
                                    </table>
                                </div>

                                <div class="input_box">
                                    <table>
                                        <th>傾斜角度</th>
                                        <td>
                                            <select name="cmbAngle" style="width: calc(100% - 1.6em)">
                                                <option value="">選択する</option>
                                                <option value="0">0</option>
                                                <option value="10">10</option>
                                                <option value="20">20</option>
                                                <option value="30">30</option>
                                                <option value="40">40</option>
                                            </select>
                                            <span style="margin-left: 5px;">度</span>
                                            <div class="error-hidden err_Angle">傾斜角度を選択してください。</div>
                                        </td>
                                    </table>
                                </div>

                                <div class="input_box">
                                    <table>
                                        <th>設置方位</th>
                                        <td>
                                            <select name="cmbDirection">
                                                <option value="">選択する</option>
                                                <option value="南">南</option>
                                                <option value="南西">南西</option>
                                                <option value="西">西</option>
                                                <option value="北西">北西</option>
                                                <option value="北">北</option>
                                                <option value="北東">北東</option>
                                                <option value="東">東</option>
                                                <option value="南東">南東</option>
                                            </select>
                                            <div class="error-hidden err_Direction">設置方位を選択してください。</div>
                                        </td>
                                    </table>
                                </div>

                                <div class="input_box">
                                    <table>
                                        <th>電力使用区分</th>
                                        <td>
                                            <select name="cmbUsage">
                                                <option value="">選択する</option>
                                                <option value="自家消費">自家消費</option>
                                                <option value="自家消費余剰売電">自家消費余剰売電</option>
                                                <option value="全量買電">全量買電</option>
                                            </select>
                                            <div class="error-hidden err_Usage">電力使用区分を選択してください。</div>
                                        </td>
                                    </table>
                                </div>
                            </div>
                            <div class="right">
                                <img src="/img/input_placeinfo.png" alt="">
                            </div>
                        </div>
                    </div>
                </section>
            </section>

            <section class="run">
                <div class="cont">
                    <button type="submit">シミュレーション実行</button>
                </div>    
            </section>
        </form>
    </main>
<?php
get_footer();
?>
<script>
    var aryArea = {
        '北海道':'hokkaido',
        '東北':'tohoku',
        '北陸':'hokuriku',
        '関東':'kanto',
        '中部':'chubu',
        '近畿':'kinki',
        '中国':'chugoku',
        '四国':'shikoku',
        '九州':'kyushu',
        '沖縄':'okinawa',
    };

    $('[name="cmbArea"]').on('change', function() {
        // エリアが変更された場合
        var strChooseArea = $(this).val();

        if (strChooseArea && aryArea[strChooseArea]) {
            var imgSrc = "/img/choose_" + aryArea[strChooseArea] + ".png";
            $('#japanmap').attr('src', imgSrc);
        } else {
            $('#japanmap').attr('src', "/img/japan_map.png"); // Default image
        }
    });
</script>