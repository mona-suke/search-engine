//データベースから品種を取得する場合のサンプル

<?php
	function set_list($lists,$genre_name){
		try {
			foreach($lists as $record){
				if ($record['genre']==$genre_name){
					echo "<li class='{$record['genre']}' data-id='{$record['species_id']}'>{$record['species_name']}</li>";
				}
			}
		} catch(PDOException $e) {
		echo "エラーメッセージ : " . $e -> getMessage();
		}	
	}

?>
