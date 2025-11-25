//デモデータ

<?php
header('Content-Type: application/json; charset=utf-8');

try {

    // デモ用の地域マスタ
    $regions_master = [
        '1' => 'Tokyo',
        '2' => 'Osaka',
        '3' => 'Nagoya',
        '4' => 'Hukuoka',
        '5' => 'Sapporo',
        '6' => 'Sendai'
    ];

    // 件数（デフォルト全件／指定があればランダム抽出数）
    $limit = isset($_GET['limit']) && is_numeric($_GET['limit'])
        ? (int)$_GET['limit']
        : count($regions_master);

    $limit = max(1, min($limit, count($regions_master)));

    // ランダムに地域を抽出
    $keys = array_keys($regions_master);
    shuffle($keys);
    $keys = array_slice($keys, 0, $limit);

    // JSON用配列を作成
    $data = [];

    foreach ($keys as $id) {
        $data[] = [
            'regions_id'   => $id,
            'regions_name' => $regions_master[$id]
        ];
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

