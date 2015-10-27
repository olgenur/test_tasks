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
		if($local_user['token'] != get_user_token($local_user['id']))
				json_answer(0);
	}
	else json_answer(0);

	if(!isset($_POST['message'])) json_answer(0);
	$msg = strip_tags($_POST['message'],'<b><i><u><pre>');
	$to_user_id = $_POST['to_user_id'];
	if($to_user_id==0){
		$type_id = 1;
		$to_user_id = 'NULL';
	}
	else
		$type_id = 0;

include 'connect.php';
$query_data = 'mes_date=CURRENT_TIMESTAMP, mes_text='.str_esc($msg)
						.', from_user_id='.num_esc($_SESSION[$key_name]['id'])
						.', to_user_id='.num_esc($to_user_id)
						.', room_id='.num_esc($_SESSION[$key_name]['room_id'])
						.', type_id='.num_esc($type_id);
//echo 'INSERT INTO message SET '.$query_data;
$con->query('INSERT INTO message SET '.$query_data);

json_answer(1);
?>
