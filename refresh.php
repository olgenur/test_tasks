<?php
	include 'init_session.php';
	header("Content-type: text/plain; charset=utf8");
	header("Cache-control: no-store, no-cache, must-revalidate");
	header("Cache-control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	function return_zero(){
			echo json_encode(array(
				'zero' => true,
				'last' => time()
			));
			die();
	}
	function return_kickout(){
			global $key_name;
			unset($_SESSION[$key_name]);
			echo json_encode(array(
				'zero' => false,
				'kickout' => true
			));
			die();
	}
	if(isset($_SESSION[$key_name])){
		$local_user = $_SESSION[$key_name];
		if($local_user['token'] != get_user_token($local_user['id'])){
			unset($_SESSION[$key_name]);
			return_zero();
		}
	}
	else return_zero();

	if(!isset($_POST['last_date']) || !isset($_POST['to_user_id'])) return_zero();

	include 'connect.php';
	$room_id = $_SESSION[$key_name]['room_id'];
	$my_id = $_SESSION[$key_name]['id'];
	$last_date = $_POST['last_date'];//1445877512;
	$to_user_id = $_POST['to_user_id']; //0
	$users_list = $_POST['users_list']; //"[2]"

	/*
	$r = $con->query("SELECT user_id FROM `user` WHERE user_blocked=1 AND user_id=".num_esc($my_id));
	//ПОЧЕМУ affected_rows == 0 ПРИ НАЛИЧИИ ДАННЫХ ????????
	if($con->affected_rows > 1)	return_kickout();
	*/
	//А ТАК РАБОТАЕТ НО КАСТЫЛЬНО :(
	$r = $con->query("SELECT user_blocked FROM `user` WHERE user_id=".num_esc($my_id));
	$uinfo = $r->fetch_assoc();
	if($uinfo['user_blocked'] == 1)	return_kickout();

	function return_messages($msgs_array, $del_msgs_array, $gone_users_array, $new_users_array){
			echo json_encode(array(
				'zero' => false,
				'msgs' => $msgs_array,
				'delmsgs' => $del_msgs_array,
				'gone' => $gone_users_array, //id list
				'new' => $new_users_array, //{id, name, ...} list
				'kickout' => false,
				'last' => time() //current UNIX_TIMESTAMP like 1445708845
			));
			die();
	}

	$msg_array = array();
	if($to_user_id>0){ //privat chat, no admin messages
			$select = "mes_id, mes_text, mes_date, from_user_id, type_id FROM `message`";
			$where = "type_id = 0 AND ((to_user_id = ".num_esc($my_id)
				." AND from_user_id = ".num_esc($to_user_id).") OR"
				." (to_user_id = ".num_esc($to_user_id)." AND from_user_id = ".num_esc($my_id)."))";
	}
	else{ // room chat
			$select = "m.mes_id, m.mes_text, m.mes_date, m.from_user_id, m.type_id, "
											."u.user_type, u.user_name FROM `message` m, `user` u";
			$where = "((m.type_id = 1 AND m.room_id = ".num_esc($room_id).") "
							."OR m.type_id = 2 OR (m.type_id = 0 AND to_user_id = "
							.num_esc($my_id).")) AND m.from_user_id = u.user_id AND u.user_blocked=0";
	}
	$r = $con->query(
		"SELECT {$select} WHERE {$where} AND UNIX_TIMESTAMP(mes_date)>".num_esc($last_date)
		//if mes_date > $last_date then message is new
		//may be better use mes_id and last_mes_id
	);
	while ($m = $r->fetch_assoc()) {
		array_push($msg_array, $m);
	}

	$gone_users_array = array();
	$new_users_array = array();
	$del_msg_array = array();

	if($to_user_id==0){ // room chat
			$check_list = substr($users_list,1,strlen($users_list)-2);
			if(strlen($check_list)>0){ // if somebody in room find who gone
				$select = "SELECT user_id FROM `user`";
				$where = " WHERE user_id IN (".$check_list.") AND (use_room_id<>"
									.num_esc($room_id).' OR use_room_id IS NULL)';
				//echo '<h2>'.$select.$where.'</h2>';
				$r = $con->query($select.$where);
				while ($u = $r->fetch_assoc()) {
					array_push($gone_users_array, $u['user_id']);
				}
			}	//find who gone
			$select = "user_id, user_type, user_name";
			if(strlen($check_list)>0){
				$check_list .= ','.num_esc($my_id);
			}
			else
				$check_list = num_esc($my_id);
			$where = "UNIX_TIMESTAMP(switch_date)>".num_esc($last_date)
				." AND use_room_id=".num_esc($room_id)
				." AND user_id NOT IN (".$check_list.")";
			$r = $con->query("SELECT {$select} FROM `user` WHERE {$where}");
			while ($u = $r->fetch_assoc()) {
				array_push($new_users_array, $u);
			}

			$r = $con->query(
				"SELECT mes_id FROM `del_msg_log` WHERE UNIX_TIMESTAMP(action_date)>".num_esc($last_date)
			);//1445877512
			if($con->affected_rows>0){
				while ($row = $r->fetch_assoc()) {
					array_push($del_msg_array, $row['mes_id']);
				}
			}
	}


	$r->free();

return_messages($msg_array, $del_msg_array, $gone_users_array, $new_users_array);
?>
