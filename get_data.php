<?php
header('Content-Type: application/json; charset=utf-8');

try {
    
    require_once 'config_ferrous.php';

	$table_name=isset($_POST['table']) ? $_POST['table'] : null;
    $species_id = isset($_POST['species_list'])  && is_array($_POST['species_list']) ? $_POST['species_list'] : [];

	$species_id = array_values(array_filter($species_id, 'strlen'));
	$in_spc=implode(',', array_fill(0, count($species_id), '?'));
	//echo "console.log('" . addslashes($in_spc) . "');";
	//echo "console.log('" . addslashes($species_id) . "');";

    if (!$species_id) {
        echo json_encode([]);
        exit;
    }
	$regions_list = isset($_POST['regions_list']) && is_array($_POST['regions_list']) ? $_POST['regions_list'] : [];
	$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
	$end_date   = isset($_POST['end_date']) ? $_POST['end_date'] : null;
	$limit = isset($_POST['limit']) && is_numeric($_POST['limit']) ? (int)$_POST['limit'] : null;
	
	// 指定テーブルに regions_idカラムがあるか確認
	$hasRegion = false;
	$stmt = $dbh->query("SHOW COLUMNS FROM $table_name LIKE 'regions_id'");
	if ($stmt->fetch()) {
		$hasRegion = true;
		
		$fields_sql="DATE_FORMAT({$table_name}.date, '%Y/%m/%d')AS date,species_name,regions_name,{$table_name}.price";
	}else{
		$columns_map = [
			'jp_scrap'  => ["price1","price2"],
			'wangan'  => ["lowprice","highprice"],
			// 必要なテーブルを全部書いていく
		];
		$subfields=array_map(fn($field) => "{$table_name}.{$field}", $columns_map[$table_name]);
		$fields=array_merge(["DATE_FORMAT({$table_name}.date, '%Y/%m/%d')AS date","species_name"],$subfields);
		$fields_sql = implode(", ", $fields);

	}

	// ベースSQL
	$sql = "SELECT $fields_sql FROM $table_name 
			INNER JOIN species_id ON $table_name.species_id = species_id.species_id 
			WHERE $table_name.species_id IN ($in_spc)";

	if ($hasRegion) {
		// 地域カラムあり
		$sql = "SELECT $fields_sql FROM $table_name 
				INNER JOIN species_id ON $table_name.species_id = species_id.species_id 
				INNER JOIN regions_id ON $table_name.regions_id = regions_id.regions_id 
				WHERE $table_name.species_id IN ($in_spc)";
	}
           
    $params = $species_id;

    // 地域で絞り込み（選択がある時のみ）
    if (!empty($regions_list)) {
        $regions_list = array_values(array_filter($regions_list, 'strlen'));
        if (count($regions_list) > 0) {
            $in = implode(',', array_fill(0, count($regions_list), '?'));
            $sql .= " AND $table_name.regions_id IN ($in)";
            $params = array_merge($params, $regions_list);
        }
	}

    // 期間で絞り込み（両方ある時のみ）
    if (!empty($start_date) && !empty($end_date)) {
        $sql .= " AND $table_name.date BETWEEN ? AND ?";
        $params[] = $start_date;
        $params[] = $end_date;
    }
	// 上限設定
	if (!empty($limit)) {
		$limit = max(1, min($limit, 10000));
		$sql .= " ORDER BY $table_name.date DESC LIMIT $limit";
	} else {
		$sql .= " ORDER BY $table_name.date DESC"; // 全件取得
	}
	//echo "console.log('" . addslashes($sql) . "');";
	//echo "console.log('" . addslashes($params) . "');";
    $stmt = $dbh->prepare($sql);
    $stmt->execute($params);
	
	$rawData = $stmt->fetchAll(PDO::FETCH_ASSOC);
	//echo "console.log('" . addslashes($sql) . "');";
	$columns = array_keys($rawData[0]);
	if(in_array("regions_name",$columns)){

		// クロス集計用に変換
		$data = [];
		$regions_list = [];
		$species_list = [];

		foreach ($rawData as $row) {
			$date = $row['date'];
			$species = $row['species_name'];
			$region = $row['regions_name'];
			$price = $row['price'];

			$data[$date][$species][$region] = $price;
			if (!in_array($region, $regions_list)) {
				$regions_list[] = $region;
			}
			if (!in_array($species,$species_list)){
				$species_list[]=$species;
			}
		}
		// 並び替え
		// rsort($regions_list);
		
		echo json_encode([
			'mode'=> 'cross',
			'species' => $species_list,
			'regions' => $regions_list,
			'data' => $data
		]);
	}else{
		// ノーマル表示用
		$display_columns_map = [
			'price1'       => '価格(円)',
			'price2'       => '価格（ドル）',
			'lowprice'       => '安値',
			'highprice'       => '高値'
		];
		
		$data=[];
		$species_list=[];
		$columns_list=[];
		
		
		foreach($rawData as $row){
			$date= $row['date'];
			$species=$row['species_name'];
			
			foreach($row as $col => $val){
				if (in_array($col, ['date','species_name'])) continue;

            	$colName = $display_columns_map[$col] ?? $col;
            	$data[$date][$species][$colName] = $val;

            	if (!in_array($colName, $columns_list)) {
                	$columns_list[] = $colName;
            	}
            	if (!in_array($species, $species_list)) {
                	$species_list[] = $species;
            	}

			}
		}
		
		echo json_encode([
			'mode'=> 'normal',
			'species' =>$species_list,
			'columns'=> $columns_list,
			'data'=> $data
		]);
	}
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}