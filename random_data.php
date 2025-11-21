<?php

header('Content-Type: application/json; charset=utf-8');

	// データの生成実行
	$initialPrice = 1000.00; // 初値
	$days = 100; // 100日分生成

    $rawData = [];
    $currentPrice = $initialPrice;
	$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;

    for ($i = 0; $i < $days; $i++) {


        $osaka = mt_rand(1000, 5000);
        $nagoya = mt_rand(1000, 5000);
        $tokyo = mt_rand(1000, 5000);
        $close =mt_rand(1000, 5000);

        $$rawData[] = [
            'date' => $start_date,
            '大阪' => $osaka,
            '名古屋' => $nagoya,
            '東京' => $tokyo,
        ];

    }

    echo json_encode([
			'data'=> $data
		]);


    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
