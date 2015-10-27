<?php
		include 'init_session.php';
		header("Content-type: text/plain; charset=utf8");
		header("Cache-control: no-store, no-cache, must-revalidate");
		header("Cache-control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		function json_answer($answer){
				echo json_encode(array('answer' => $answer));
				die();
		}
		if(isset($_SESSION[$key_name])){
			$local_user = $_SESSION[$key_name];
			if($local_user['token'] != get_user_token($local_user['id']) || $local_user['type'] != 1)
					json_answer(0);
		}
		else json_answer(0);

		if(!isset($_GET['mesid'])) json_answer(0);

		include 'connect.php';
		$mes_id = num_esc($_GET['mesid']);


		$con->query("DELETE FROM message WHERE mes_id=".$mes_id);
		if($con->affected_rows > 0){
				$con->query('INSERT INTO del_msg_log SET mes_id='.$mes_id);
				json_answer(1);
		}
		json_answer(0);
?>
