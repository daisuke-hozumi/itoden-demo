<?php
/**
 * The template Name: シミュレーション結果
 *
 * @package hs
 */
// POSTデータが存在しない場合は入力ページにリダイレクト
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /simulation/');
    exit();
}

get_header();
?>
<?php
    // 入力データの取得
    $installation_area = isset($_POST['txtMenseki']) ? (float)$_POST['txtMenseki'] : 0; // 設置面積 (㎡)
    $chosen_roof_type = isset($_POST['cmbPlace']) ? $_POST['cmbPlace'] : ''; // 屋根形状
    $unit_cost = isset($_POST['txtTanka']) ? (float)$_POST['txtTanka'] : 0; // 電力料金単価
    $region = isset($_POST['cmbArea']) ? $_POST['cmbArea'] : ''; // 地域
    $angle = isset($_POST['cmbAngle']) ? $_POST['cmbAngle'] : '0'; // 傾斜角度
    $direction = isset($_POST['cmbDirection']) ? $_POST['cmbDirection'] : ''; // 傾斜角度
    $usage = isset($_POST['cmbUsage']) ? $_POST['cmbUsage'] : ''; // 使用区分

    // 固定値（全体シートのデータに基づく）
    $module_unit_price = get_field('module_unit_price','option'); // パネル単価 (円/W)
    $module_unit_price = $module_unit_price * 1000; // kWに変換
    $module_per_sqm = get_field('module_per_sqm','option'); // 太陽光モジュール容量 (W/㎡)
    $pcs_unit_price = get_field('pcs_unit_price','option'); // パワコン単価 (円/kW)
    $kasekisai = get_field('kasekisai','option'); // 過積載率
    $pv_length_kw = get_field('pv_cable_length','option'); // PV延長ケーブル(kWあたり)
    $kadai_kw = get_field('kadai','option'); // 架台(kWあたり)
    $pv_cable_unit_price = get_field('pv_cable_unit_price','option'); // PVケーブル単価 (円/m)
    // ACFデータの取得
    $rack_pricing = [];
    if (have_rows('rack_pricing', 'option')) {
        while (have_rows('rack_pricing', 'option')) {
            the_row();
            $roof_type = get_sub_field('roof_type');
            $price = (float) get_sub_field('price');
            if ($roof_type && $price) {
                $rack_pricing[$roof_type] = $price;
            }
        }
    }

    $transformer_tiers = [];
    if (have_rows('transformer_tiers', 'option')) {
        while (have_rows('transformer_tiers', 'option')) {
            the_row();
            $capacity = (int) get_sub_field('capacity');
            $price = (float) get_sub_field('price');
            $transformer_tiers[$capacity] = $price;
        }
    }

    $installation_unit_price = get_field('installation_unit_price','option'); // 設置工事費単価 (円/kW)
    $electric_work_unit_price = get_field('electric_work_unit_price','option'); // 電気工事費単価 (円/kW)
    $equipment_installation_unit_price = get_field('equipment_installation_unit_price','option'); // 機器設置費単価 (円/kW)
    $transport_unit_price = get_field('transport_unit_price','option'); // 機材搬入搬出費単価 (円/kW)
    $miscellaneous_rate = get_field('miscellaneous_rate','option') / 100; // 諸経費率

    // 固定費用の個別項目
    $power_conditioner_management_cost = get_field('power_conditioner_management_cost','option'); // パワコン管理装置
    $power_distribution_board_cost = 200000; // 交流集電盤 todo:ここは直値？
    $control_unit_cost = get_field('control_unit_cost','option'); // RPR/OVGR制御ユニット
    $cubicle_expansion_cost = get_field('cubicle_expansion_cost','option'); // 既設キュービクル盤機能増設費
    $design_and_documents_cost = get_field('design_and_documents_cost','option'); // 設計・各種申請・書類作成費
    $safety_measures_cost = get_field('safety_measures_cost','option'); // 安全対策費
    $inspection_cost = get_field('inspection_cost','option'); // 竣工検査費
    $document_submission_cost = get_field('document_submission_cost','option'); // 使用前自己確認届出書作成費
    $transport_add_cost = get_field('transport_add_cost','option'); // 機材搬入搬出費（追加分）

    // 設置パネル容量 (kW)
    $panel_capacity = ($installation_area * $module_per_sqm) / 1000;

    // RPR/OVGR制御ユニット費用
    if ($usage !== "自家消費" || $panel_capacity <= 50) {
        // 自家消費ではない、または設置パネル容量が50kW以下の場合
        
        // RPR/OVGR制御ユニット費、既設キュービクル盤機能増設費を0に
        $control_unit_cost = 0;
        $cubicle_expansion_cost = 0;
    }

    // 太陽光モジュール費用
    $module_cost = $panel_capacity * $module_unit_price;

    // パワーコンディショナー容量と費用
    $pcs_capacity = $panel_capacity / ($kasekisai / 100); // 過積載率140%
    $pcs_cost = $pcs_capacity * $pcs_unit_price;

    // PV延長ケーブル費用
    $pv_cable_length = $panel_capacity * $pv_length_kw;
    $pv_cable_cost = $pv_cable_length * $pv_cable_unit_price;

    // 屋根用太陽光架台費用
    $rack_unit_price = $rack_pricing[$chosen_roof_type] ?? 1500; // 屋根形状別単価
    $rack_quantity = $panel_capacity * $kadai_kw; // 8個/kW
    $rack_cost = $rack_quantity * $rack_unit_price;

    // 屋根金具・パネル設置工事費
    $roof_installation_cost = $panel_capacity * $installation_unit_price;

    // 電気工事費
    $electric_work_cost = $panel_capacity * $electric_work_unit_price;

    // 各種機器設置工事費
    $additional_installation_cost = $panel_capacity * $equipment_installation_unit_price;

    // ダウントランス費用（パワコン容量基準）
    $transformer_cost = 0;
    if ($pcs_capacity >= 50) {
        foreach ($transformer_tiers as $tier_capacity => $tier_cost) {
            if ($pcs_capacity >= $tier_capacity) {
                $transformer_cost = $tier_cost;
            }
        }
    }

    // 機材搬入搬出費
    $transport_cost = $panel_capacity * $transport_unit_price + $transport_add_cost;

    // 合計固定費用
    $fixed_cost = $power_conditioner_management_cost + 
                  $power_distribution_board_cost + 
                  $control_unit_cost + 
                  $cubicle_expansion_cost + 
                  $design_and_documents_cost + 
                  $safety_measures_cost + 
                  $inspection_cost + 
                  $document_submission_cost;
    // 工事費用合計
    $construction_cost = $module_cost + $pcs_cost + $pv_cable_cost + $rack_cost + $electric_work_cost + $additional_installation_cost + $roof_installation_cost + $transformer_cost + $transport_cost + $fixed_cost;

    // 諸経費
    $miscellaneous_cost = $construction_cost * $miscellaneous_rate;

    // 総費用
    $total_cost = $construction_cost + $miscellaneous_cost;

    // 年間予想発電量 (仮: 年間日射量係数1200kWh/kW)
    $annual_generation = $panel_capacity * 1200;

    // 年間予想削減費
    $annual_savings = $annual_generation * $unit_cost;

    // 月間削減費
    $monthly_savings = $annual_savings / 12;

    // 累積削減費と損益分岐点
    $cumulative_savings = [];
    $months_needed = 0;
    $max_months = 72;

    for ($i = 0; $i < $max_months; $i++) {
        $total_savings = ($i + 1) * $monthly_savings;
        $cumulative_savings[] = $total_savings;

        if ($total_savings >= $total_cost && $months_needed === 0) {
            $months_needed = $i + 1;
        }

        if ($months_needed > 0 && $i >= $months_needed + 2) {
            break;
        }
    }

    // 結果表示用のフォーマット
    $formatted_panel_capacity = number_format($panel_capacity, 1);
    $formatted_total_cost = number_format($total_cost);
    $formatted_annual_generation = number_format($annual_generation, 0);
    $formatted_annual_savings = number_format($annual_savings, 0);
    $cumulative_savings_json = json_encode($cumulative_savings);
    $total_cost_json = json_encode($total_cost);
    $labels_json = json_encode(array_map(function($month) {
        return ($month + 1) . '月';
    }, range(0, count($cumulative_savings) - 1)));

// デバッグ用: 工事費用の計算結果を確認
/*
echo '<pre>';
echo "Panel Capacity: $panel_capacity\n";
echo "Module Cost: $module_cost\n";
echo "PCS Cost: $pcs_cost\n";
echo "PV Cable Cost: $pv_cable_cost\n";
echo "Rack Cost: $rack_cost\n";
echo "Electric Work Cost: $electric_work_cost\n";
echo "Equipment Installation Cost: $equipment_installation_cost\n";
echo "Transport Cost: $transport_cost\n";
echo "Fixed Cost: $fixed_cost\n";
echo "Construction Cost: $construction_cost\n";
echo "Miscellaneous Cost: $miscellaneous_cost\n";
echo "Total Cost: $total_cost\n";
echo '</pre>';
*/
?>

<style>
    .meisai {
        display: none;
    }
    .meisai tr td:nth-of-type(n+4) {
        text-align: right;
    }
</style>
<section class="mt65 mb35 cont meisai">
    <h2>※※ テスト確認用 ※※見積明細表</h2>
    <div class="inner">
        <table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr>
                    <th style="padding: 8px;">項目</th>
                    <th style="padding: 8px;">数量</th>
                    <th style="padding: 8px;">単位</th>
                    <th style="padding: 8px;">単価 (円)</th>
                    <th style="padding: 8px;">金額 (円)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 8px;">太陽光モジュール</td>
                    <td style="padding: 8px;"><?= number_format($panel_capacity, 2) ?></td>
                    <td style="padding: 8px;">kW</td>
                    <td style="padding: 8px;"><?= number_format($module_unit_price) ?></td>
                    <td style="padding: 8px;"><?= number_format($module_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">パワーコンディショナー</td>
                    <td style="padding: 8px;"><?= number_format($pcs_capacity, 2) ?></td>
                    <td style="padding: 8px;">kW</td>
                    <td style="padding: 8px;"><?= number_format($pcs_unit_price) ?></td>
                    <td style="padding: 8px;"><?= number_format($pcs_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">パワコン管理装置</td>
                    <td style="padding: 8px;">1</td>
                    <td style="padding: 8px;">個</td>
                    <td style="padding: 8px;"><?= number_format($power_conditioner_management_cost) ?></td>
                    <td style="padding: 8px;"><?= number_format($power_conditioner_management_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">PV延長ケーブル</td>
                    <td style="padding: 8px;"><?= number_format($pv_cable_length, 0) ?></td>
                    <td style="padding: 8px;">m</td>
                    <td style="padding: 8px;"><?= number_format($pv_cable_unit_price) ?></td>
                    <td style="padding: 8px;"><?= number_format($pv_cable_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">屋根用太陽光架台</td>
                    <td style="padding: 8px;"><?= number_format($rack_quantity, 0) ?></td>
                    <td style="padding: 8px;">個</td>
                    <td style="padding: 8px;"><?= number_format($rack_unit_price) ?></td>
                    <td style="padding: 8px;"><?= number_format($rack_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">交流集電盤</td>
                    <td style="padding: 8px;">1</td>
                    <td style="padding: 8px;">面</td>
                    <td style="padding: 8px;"><?= number_format($power_distribution_board_cost) ?></td>
                    <td style="padding: 8px;"><?= number_format($power_distribution_board_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">ダウントランス（440/200V）</td>
                    <td style="padding: 8px;"><?= $pcs_capacity >= 50 ? '1' : '0' ?></td>
                    <td style="padding: 8px;">式</td>
                    <td style="padding: 8px;"><?= $pcs_capacity >= 50 ? number_format($transformer_cost) : '-' ?></td>
                    <td style="padding: 8px;"><?= number_format($transformer_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">RPR／OVGR制御ユニット</td>
                    <td style="padding: 8px;">1</td>
                    <td style="padding: 8px;">式</td>
                    <td style="padding: 8px;"><?= number_format($control_unit_cost) ?></td>
                    <td style="padding: 8px;"><?= number_format($control_unit_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">屋根設置工事費</td>
                    <td style="padding: 8px;">1</td>
                    <td style="padding: 8px;">式</td>
                    <td style="padding: 8px;"><?= number_format($roof_installation_cost) ?></td>
                    <td style="padding: 8px;"><?= number_format($roof_installation_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">電気工事費</td>
                    <td style="padding: 8px;"><?= number_format($panel_capacity, 2) ?></td>
                    <td style="padding: 8px;">kW</td>
                    <td style="padding: 8px;"><?= number_format($electric_work_unit_price) ?></td>
                    <td style="padding: 8px;"><?= number_format($electric_work_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">各種機器設置工事</td>
                    <td style="padding: 8px;"><?= number_format($panel_capacity, 2) ?></td>
                    <td style="padding: 8px;">kW</td>
                    <td style="padding: 8px;"><?= number_format($equipment_installation_unit_price) ?></td>
                    <td style="padding: 8px;"><?= number_format($additional_installation_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">既設キュービクル盤機能増設</td>
                    <td style="padding: 8px;">1</td>
                    <td style="padding: 8px;">式</td>
                    <td style="padding: 8px;"><?= number_format($cubicle_expansion_cost) ?></td>
                    <td style="padding: 8px;"><?= number_format($cubicle_expansion_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">設計・各種申請・書類作成</td>
                    <td style="padding: 8px;">1</td>
                    <td style="padding: 8px;">式</td>
                    <td style="padding: 8px;"><?= number_format($design_and_documents_cost) ?></td>
                    <td style="padding: 8px;"><?= number_format($design_and_documents_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">安全対策費</td>
                    <td style="padding: 8px;">1</td>
                    <td style="padding: 8px;">式</td>
                    <td style="padding: 8px;"><?= number_format($safety_measures_cost) ?></td>
                    <td style="padding: 8px;"><?= number_format($safety_measures_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">機材搬入搬出費</td>
                    <td style="padding: 8px;">1</td>
                    <td style="padding: 8px;">式</td>
                    <td style="padding: 8px;"><?= number_format($transport_cost) ?></td>
                    <td style="padding: 8px;"><?= number_format($transport_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">竣工検査費</td>
                    <td style="padding: 8px;">1</td>
                    <td style="padding: 8px;">式</td>
                    <td style="padding: 8px;"><?= number_format($inspection_cost) ?></td>
                    <td style="padding: 8px;"><?= number_format($inspection_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">使用前自己確認届出書作成</td>
                    <td style="padding: 8px;">1</td>
                    <td style="padding: 8px;">式</td>
                    <td style="padding: 8px;"><?= number_format($document_submission_cost) ?></td>
                    <td style="padding: 8px;"><?= number_format($document_submission_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;">諸経費</td>
                    <td style="padding: 8px;" colspan="3"></td>
                    <td style="padding: 8px;text-align: right"><?= number_format($miscellaneous_cost) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px; font-weight: bold;">合計</td>
                    <td style="padding: 8px;" colspan="3"></td>
                    <td style="padding: 8px; font-weight: bold; text-align: right"><?= number_format($total_cost) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<main>
    <style>
        .mv h2 {
            background-image: url(/img/mv_ret.png);
            font-size: calc(50px - (1023px - 100vw) * 0.03318);
            color: #fff;
            text-align: center;
            padding: 1em;
            font-weight: 600;
            background-position: right;
            background-size: cover;
        }
        @media screen and (min-width:1024px) {
            .mv h2 {
                font-size: 50px;
            }
        }
    </style>
        <section class="mv">
            <h2 style="background-image: url(/img/mv_ret.png)">シミュレーション結果</h2>
        </section>

        <div class="cont">
        <!-- 計算結果表示 -->
        <section class="mt65 mb35">
            <div class="inner results-box">
                <div>
                    <h3>設置パネル容量</h3>
                    <p><?= $formatted_panel_capacity ?> kW</p>
                </div>
                <div>
                    <h3>工事費用</h3>
                    <p><?= $formatted_total_cost ?> 円</p>
                </div>
                <div>
                    <h3>年間予想発電量</h3>
                    <p><?= $formatted_annual_generation ?> kWh</p>
                </div>
                <div>
                    <h3>年間予想削減費</h3>
                    <p><?= $formatted_annual_savings ?> 円</p>
                </div>
            </div>
        </section>

        <section class="sec-input_box">
            <h2>年間削減電気料金</h2>
            <!-- キャンバスの親要素の高さを制限 -->
            <div class="js-scrollable inner" style="padding: 35px;overflow-x: scroll">
                <div style="width: 800px; max-width: 800px; height: 400px; margin: auto;">
                    <canvas id="savingsChart"></canvas>
                </div>
            </div>
        </section>
        <section class="run">
            <div class="cont">
                <a href="https://www.itoden-eng.co.jp/contact/form.cgi">お問い合わせはこちら</a>
            </div>    
        </section>
    </main>

    <!-- グラフ用のChart.jsとプラグイン読み込み -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>

    <script>
        // PHPからのデータをJavaScriptに渡す
        const cumulativeSavings = <?= $cumulative_savings_json ?>;
        const constructionCost = <?= $total_cost_json ?>;
        const labels = <?= $labels_json ?>;

        const ctx = document.getElementById('savingsChart').getContext('2d');

        // 損益分岐点を超えた月の色を設定
        const barColors = cumulativeSavings.map(value => value >= constructionCost ? 'rgba(255, 99, 132, 0.7)' : 'rgba(0, 128, 255, 0.6)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '月別削減費',
                    data: cumulativeSavings,
                    backgroundColor: barColors,
                    borderColor: 'rgba(0, 128, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true, // レスポンシブ対応
                maintainAspectRatio: true, // アスペクト比を維持する（これで無限伸びを防ぐ）
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: Math.max(...cumulativeSavings, constructionCost) * 1.2,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + '円';
                            }
                        }
                    }
                },
                plugins: {
                    annotation: {
                        annotations: [
                            {
                                type: 'line',
                                yMin: constructionCost,
                                yMax: constructionCost,
                                borderColor: 'orange',
                                borderWidth: 2,
                                label: {
                                    content: '工事費用',
                                    enabled: true,
                                    position: 'end',
                                    backgroundColor: 'orange',
                                    color: 'white',
                                    padding: 5
                                }
                            }
                        ]
                    },
                    legend: {
                        display: true,
                        labels: {
                            generateLabels: function(chart) {
                                const originalLabels = Chart.defaults.plugins.legend.labels.generateLabels(chart);
                                return [
                                    ...originalLabels,
                                    {
                                        text: '損益分岐点を超えた月',
                                        fillStyle: 'rgba(255, 99, 132, 0.7)',
                                        strokeStyle: 'rgba(255, 99, 132, 1)',
                                        hidden: false,
                                        lineCap: 'round',
                                        pointStyle: 'rect',
                                        datasetIndex: null
                                    },
                                    {
                                        text: '工事費用',
                                        fillStyle: 'orange',
                                        strokeStyle: 'orange',
                                        borderWidth: 2,
                                        lineCap: 'round',
                                        pointStyle: 'line',
                                        datasetIndex: null
                                    }
                                ];
                            }
                        }
                    }
                }
            }
        });
    </script>
<?php
get_footer();
