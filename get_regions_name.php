<?php
header('Content-Type: application/json; charset=utf-8');

try {
    require_once 'config_ferrous.php';

	$sql = "SELECT regions_id, regions_name FROM regions_id";
	$stmt = $dbh->query($sql);

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}