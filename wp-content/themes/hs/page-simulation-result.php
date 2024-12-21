<?php
/**
 * The template Name: シミュレーション結果
 *
 * @package hs
 */

get_header();
?>
<?php 
    // POSTデータが存在しない場合は入力ページにリダイレクト
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /simulation/');
        exit();
    }

    // 入力データの取得
    $installation_area = isset($_POST['txtMenseki']) ? (float)$_POST['txtMenseki'] : 0; // 設置面積
    $roof_type = isset($_POST['cmbPlace']) ? $_POST['cmbPlace'] : ''; // 屋根形状
    $unit_cost = isset($_POST['txtTanka']) ? (float)$_POST['txtTanka'] : 0; // 電力料金単価
    $region = isset($_POST['cmbArea']) ? $_POST['cmbArea'] : '全国'; // 地域（仮: 全国） todo: 仮データ

    // 固定データ (Excelの内容を仮設定)
    $module_per_sqm = 160.0; // 太陽光モジュール1㎡あたり容量 (kW)
    $module_unit_price = 35000.0; // 太陽光モジュール単価 (円/kW)
    $roof_pricing = [ // 屋根形状別単価 (仮: Excelデータ)
        "８８折板屋根" => 1506,
        "ハゼ式屋根" => 1504,
        "陸屋式屋根" => 1500,
        "野立て架台" => 1508,
        "カーポート" => 1507,
        "水上架台" => 1510
    ];
    $pcs_unit_price = 11000.0; // パワーコンディショナー単価 (円/kW)
    $annual_solar_factor = 1200; // 地域別日射量係数 (仮: 全国平均値) todo: 仮データ

    // 設置パネル容量 (kW)
    $panel_capacity = ($installation_area * $module_per_sqm) / 1000;

    // 太陽光モジュール費用
    $module_cost = $panel_capacity * $module_unit_price;

    // 屋根形状費用
    $roof_unit_price = $roof_pricing[$roof_type] ?? 0; // 未指定の場合0
    $roof_cost = $panel_capacity * $roof_unit_price;

    // パワーコンディショナー費用
    $pcs_cost = $panel_capacity * $pcs_unit_price;

    // 工事費用合計
    $construction_cost = $module_cost + $roof_cost + $pcs_cost;

    // 年間予想発電量 (kWh)
    $annual_generation = $panel_capacity * $annual_solar_factor;

    // 年間予想削減費 (円)
    $annual_savings = $annual_generation * $unit_cost;

    // 月間削減費
    $monthly_savings = $annual_savings / 12;

    // 累積削減費と損益分岐点
    $cumulative_savings = [];
    $months_needed = 0;
    $max_months = 60; // 最大60ヶ月表示

    for ($i = 0; $i < $max_months; $i++) {
        $total_savings = ($i + 1) * $monthly_savings;
        $cumulative_savings[] = $total_savings;

        if ($total_savings >= $construction_cost && $months_needed === 0) {
            $months_needed = $i + 1; // 初回損益分岐点
        }

        if ($months_needed > 0 && $i >= $months_needed + 2) {
            break;
        }
    }

    // 結果表示用のフォーマット
    $formatted_panel_capacity = number_format($panel_capacity, 1);
    $formatted_construction_cost = number_format($construction_cost);
    $formatted_annual_generation = number_format($annual_generation, 0);
    $formatted_annual_savings = number_format($annual_savings, 0);
    $cumulative_savings_json = json_encode($cumulative_savings);
    $construction_cost_json = json_encode($construction_cost);
    $labels_json = json_encode(array_map(function($month) {
        return ($month + 1) . '月';
    }, range(0, count($cumulative_savings) - 1)));
?>
<main>
        <section class="mv">
            <h2><img src="/img/mv_ret.png" alt="太陽光発電料金・発電量 シミュレーション結果" width="100%" height="auto"></h2>
        </section>

        <div class="cont">
        <!-- 計算結果表示 -->
        <section class="mt65 mb35">
            <div class="inner results-box">
                <style>
                    .results-box {
                        text-align: center;
                        max-width: 800px;
                        margin: auto;
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: space-between
                    }
                    .results-box > div {
                        width: 48.5%;
                        margin-bottom: 3%;
                        border: 1px solid #ccc;
                        padding: 0;
                    }
                    .results-box > div h3 {
                        background-color: #1FA2C3;
                        color: #fff;
                        font-weight: 600;
                        padding: 5px;
                    }
                    .results-box > div p {
                        font-size: 2em;
                        font-weight: 600;
                        padding: 1em;
                    }
                </style>
                <div>
                    <h3>設置パネル容量</h3>
                    <p><?= $formatted_panel_capacity ?> kW</p>
                </div>
                <div>
                    <h3>工事費用</h3>
                    <p><?= $formatted_construction_cost ?> 円</p>
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
            <div class="inner" style="padding: 35px;">
                <div style="width: 100%; max-width: 800px; height: 400px; margin: auto;">
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
        const constructionCost = <?= $construction_cost_json ?>;
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
