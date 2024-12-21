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
    $panel_capacity = isset($_POST['panel_capacity']) ? (float)$_POST['panel_capacity'] : 0;
    $construction_cost = isset($_POST['construction_cost']) ? (int)$_POST['construction_cost'] : 0;
    $unit_cost = isset($_POST['unit_cost']) ? (float)$_POST['unit_cost'] : 0;

    // 年間予想発電量と削減費
    $annual_generation = $panel_capacity * 0.9 * 365 * 24;
    $annual_savings = $annual_generation * $unit_cost;
    $monthly_savings = $annual_savings / 12;

    // 累積削減費と損益分岐点の月を計算
    $cumulative_savings = [];
    $months_needed = 0;
    $max_months = 60; // 表示する月の最大値

    // ループして月ごとの累積削減費を計算
    for ($i = 0; $i < $max_months; $i++) {
        $total_savings = ($i + 1) * $monthly_savings;
        $cumulative_savings[] = $total_savings;

        if ($total_savings >= $construction_cost && $months_needed === 0) {
            $months_needed = $i + 1; // 初めて損益分岐点を超えた月を記録
        }

        // 損益分岐点＋3か月を満たしたらループを終了
        if ($months_needed > 0 && $i >= $months_needed + 2) {
            break;
        }
    }

    // 表示する月数を決定
    $months_to_display = max(12, $months_needed + 3, count($cumulative_savings));
    $labels = array_map(function($month) {
        return ($month + 1) . '月';
    }, range(0, $months_to_display - 1));

    // JSONエンコードしてJavaScriptに渡す
    $cumulative_savings_json = json_encode($cumulative_savings);
    $construction_cost_json = json_encode($construction_cost);
    $labels_json = json_encode($labels);

    // 結果表示用に数値を整形（フォーマット）
    $formatted_panel_capacity = number_format($panel_capacity, 1); // 小数点1桁
    $formatted_construction_cost = number_format($construction_cost); // カンマ区切り
    $formatted_annual_generation = number_format($annual_generation, 0); // 発電量の整数値
    $formatted_annual_savings = number_format($annual_savings, 0); // 削減費の整数値

    // タイトルなどの共通変数
    $title = '年間削減電気料金シミュレーション';
    $description = '太陽光システムによる年間削減電気料金のシミュレーション結果を表示します。';
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
