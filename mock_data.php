<?php
header('Content-Type: application/json; charset=utf-8');

try {

    // POSTから値取得（使わなくても互換性のため残す）
    $table_name   = $_POST['table'] ?? 'demo_table';
    $species_ids  = $_POST['species_list'] ?? ['1', '2'];
    $regions_list = $_POST['regions_list'] ?? ['1', '2'];
    $start_date   = $_POST['start_date'] ?? date('Y-m-d', strtotime('-10 days'));
    $end_date     = $_POST['end_date'] ?? date('Y-m-d');
    $limit        = isset($_POST['limit']) ? (int)$_POST['limit'] : 30;

    $hasRegion = true; // デモでは regions を有効に

    // デモ用マスタ
    $species_master = [
        '1' => 'a',
        '2' => 'b',
        '3' => 'c',
        '4' => 'd',
        '5' => 'e',
        '6' => 'f',
        '7' => 'g',
        '8' => 'h',
        '9' => 'i',
        '10' => 'j'
    ];

    $regions_master = [
        '1' => 'tokyo',
        '2' => 'Osaka',
        '3' => 'Nagoya',
        '4' => 'Hukuoka',
        '5' => 'Sapporo',
        '6' => 'Sendai'
    ];

    // 日付リスト作成
    $dates = [];
    $current = strtotime($start_date);
    $end     = strtotime($end_date);

    while ($current <= $end) {
        $dates[] = date('Y/m/d', $current);
        $current = strtotime('+1 day', $current);
    }

    // クロス集計用データ生成
    if ($hasRegion) {
        $data = [];
        $species_list = [];
        $regions_out = [];

        foreach ($dates as $date) {
            foreach ($species_ids as $sid) {
                $species = $species_master[$sid] ?? "品種{$sid}";
                foreach ($regions_list as $rid) {
                    $region = $regions_master[$rid] ?? "地域{$rid}";
                    $price = rand(30000, 100000);

                    $data[$date][$species][$region] = $price;

                    if (!in_array($species, $species_list)) $species_list[] = $species;
                    if (!in_array($region, $regions_out)) $regions_out[] = $region;
                }
            }
        }

        echo json_encode([
            'mode'    => 'cross',
            'species' => $species_list,
            'regions' => $regions_out,
            'data'    => $data
        ], JSON_UNESCAPED_UNICODE);
    } 
    else {
        // ノーマルデータ版
        $columns = ['価格(円)', '価格（ドル）'];
        $data = [];
        $species_list = [];

        foreach ($dates as $date) {
            foreach ($species_ids as $sid) {
                $species = $species_master[$sid] ?? "品種{$sid}";

                $data[$date][$species]['価格(円)'] = rand(30000, 100000);
                $data[$date][$species]['価格（ドル）'] = rand(200, 800);

                if (!in_array($species, $species_list)) $species_list[] = $species;
            }
        }

        echo json_encode([
            'mode'    => 'normal',
            'species' => $species_list,
            'columns' => $columns,
            'data'    => $data
        ], JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
