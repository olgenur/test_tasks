<?php
		include 'init_session.php';
		init_admin();

		if(!isset($_POST['action'])) redirect_to('index.php');

		include 'connect.php';
		$action = $_POST['action'];
		if($action=='delete' && isset($_POST['room_id'])){
			$con->query("DELETE FROM room WHERE room_id=".num_esc_post('room_id'));
			$con->query("DELETE FROM message WHERE room_id=".num_esc_post('room_id'));
			redirect_to('index.php');
		}
		if(isset($_POST['title']) && isset($_POST['description'])){
			if($action=='create'){
				$con->query('INSERT INTO room SET room_name='.str_esc_post('title')
										.', room_desc='.str_esc_post('description'));
			}
			if($action=='edit' && isset($_POST['room_id'])){
				$con->query('UPDATE room SET room_name='.str_esc_post('title')
										.', room_desc='.str_esc_post('description')
										.' WHERE room_id='.num_esc_post('room_id')
							);
			}
		}
		redirect_to('index.php');
?>
