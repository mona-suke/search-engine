<?php
function set_list($genre_name){

    // デモ用の品種データ（DBの代わり）
    $lists = [
        ['genre' => 'scrap',   'species_id' => '1', 'species_name' => 'a'],
        ['genre' => 'scrap',   'species_id' => '2', 'species_name' => 'b'],
        ['genre' => 'scrap',   'species_id' => '3', 'species_name' => 'c'],
        ['genre' => 'jp_scrap',  'species_id' => '4', 'species_name' => 'd'],
        ['genre' => 'jp_scrap',  'species_id' => '5', 'species_name' => 'e'],
        ['genre' => 'jp_scrap',   'species_id' => '6', 'species_name' => 'f'],
        ['genre' => 'jp_scrap',   'species_id' => '7', 'species_name' => 'g'],
        ['genre' => 'wangan',  'species_id' => '8', 'species_name' => 'h'],
        ['genre' => 'wangan',  'species_id' => '9', 'species_name' => 'i'],
        ['genre' => 'wangan',  'species_id' => '10', 'species_name' => 'j']
    ];

    try {
        foreach ($lists as $record) {
            if ($record['genre'] === $genre_name) {
                echo "<li class='{$record['genre']}' data-id='{$record['species_id']}'>";
                echo htmlspecialchars($record['species_name'], ENT_QUOTES, 'UTF-8');
                echo "</li>";
            }
        }
    } catch (Exception $e) {
        echo "エラーメッセージ : " . $e->getMessage();
    }
}
?>
