<?php
		include 'init_session.php';
		if(!isset($_GET['room_id']))
				redirect_to('index.php');
		$room_id = $_GET['room_id'];
		init_user();

		include 'connect.php';
		$r = $con->query("SELECT * FROM room WHERE room_id=".num_esc($room_id));
		if($con->affected_rows > 0)
				$room = $r->fetch_assoc();
		else
				redirect_to('index.php');

		$con->query('UPDATE user SET use_room_id='.num_esc($room_id)
								.", switch_date=CURRENT_TIMESTAMP"
								.' WHERE user_id='.num_esc($_SESSION[$key_name]['id']));
		$_SESSION[$key_name]['room_id'] = $room_id;
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Чат-комната <?php echo $room['room_name']; ?></title>
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    <link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/messenger.css">
		<link rel="stylesheet" href="css/pretty_border.css">
	 </head>

  <body>
  <div class="container">
			<header class="row pretty_border">
				<?php
					if($local_user['type']==1){
						insert_admin_menu();
						$admin_class = ' with-admin-controls';
					}
					else
						$admin_class = '';
					insert_logout_control();

					echo "<script>
									var room_id={$room_id},
									room_name='{$room['room_name']}',
									last_date = ".time().",
									my_id = {$_SESSION[$key_name]['id']},
									admin_class = '{$admin_class}',
									to_user_id = 0;
								</script>";
				?>
				<h1><a class="btn btn-default" href="index.php?leave_room" title="ДРУГИЕ КОМНАТЫ">&Xi;</a>
					<?php
						echo " <b>{$room['room_name']}</b>: {$room['room_desc']} ";
					?>
				</h1>
			</header>

			<div class="row">
				<div class="messages-list pretty_border"><!--блок сообщений-->
					<div class="content">
						<dl>
							<?php
								$r = $con->query(
									"SELECT m.mes_id, m.mes_text, m.mes_date, m.from_user_id, m.type_id,
									u.user_type, u.user_name FROM `message` m, `user` u WHERE
									 UNIX_TIMESTAMP(m.mes_date) > ".(time()-2000)." AND
									((m.type_id = 1 AND m.room_id = ".num_esc($room_id).")
													OR m.type_id = 2 OR (m.type_id = 0 AND to_user_id = "
													.num_esc($local_user['id']).")) AND m.from_user_id = u.user_id"
								);
								//, u.user_blocked, u.use_room_id
								$my_id = $_SESSION[$key_name]['id'];
								$utype_class_filter= array(
									0 => 'user',
									1 => 'admin'
								);

								$mestype_filter= array(
									0 => '<span class="mestype_privat">ЛИЧНО ВАМ</span>',
									1 => '',
									2 => '<span class="mestype_common">ВСЕМ ПОЛЬЗОВАТЕЯМ</span>'
								);
								while ($msg = $r->fetch_assoc()) {
									if($msg['from_user_id']==$my_id){
										echo '<dt class="my-message">'.date('d.m.y H:i',strtotime($msg['mes_date']))
											.' Я '.$mestype_filter[$msg['type_id']].'</dt>';
										echo '<dd class="my-message'.$admin_class.'" id="mes'.$msg['mes_id'].'">'
												.'<div class="delete-control">X</div>'.$msg['mes_text'].'</dd>';
									}
									else{
										echo '<dt>'.date('d.m.y H:i',strtotime($msg['mes_date']))
											.' <span class="'.$utype_class_filter[$msg['user_type']].'">'
											.$msg['user_name'].'</span> '.$mestype_filter[$msg['type_id']].'</dt>';
										echo '<dd class="'.$admin_class.'" id="mes'.$msg['mes_id'].'">'
												.'<div class="delete-control">X</div>'.$msg['mes_text'].'</dd>';
									}
								}
								$r->free();
							?>
					</dl>
				</div>
			</div><!--блок сообщений-->

			<div class="contacts-list pretty_border"><!--блок собеседников-->
				<div class="content">
					<ul>
						<?php
							$r = $con->query(
								"SELECT `user_id`, `user_type`, `user_name`
								 FROM `user` WHERE use_room_id=".num_esc($room_id)
								." AND `user_blocked`=0 AND user_id <> ".$my_id
							);
							$uid_list_string = '';
							while ($user = $r->fetch_assoc()) {
								$uid_list_string .= ','.$user['user_id'];
								if($user['user_type']==0)
									$user_class = "utype_user";
								else
									$user_class = "utype_admin";
								echo '<li id="ul'.$user['user_id'].'"><a class="'.$user_class
												.'" href="privat_chat.php?'
												.'rid='.$room_id
												.'&rn='.urlencode($room['room_name'])
												.'&uid='.$user['user_id']
												.'&un='.urlencode($user['user_name'])
												.'">'.$user['user_name'].'</a></li>';
							}
							$r->free();
						?>
				</ul>
				<script>
					var uid_list = [
						<?php
							if(strlen($uid_list_string)>0) echo substr($uid_list_string,1);
							else echo '';
						?>
				];
				</script>

			</div>
		</div><!--блок собеседников-->

		</div><!--row-->

		<footer class="row message-input pretty_border">
			<form class="form-inline col-md-12">
				<textarea class="form-control col-md-9" rows="3" maxlength="200" required></textarea>
				<div class="message-action">
					<input type="submit" class="btn btn-success col-md-3" value="Оправить (Enter)" />
					<div>
						тут смайлики :)
					</div>
				</div>
			</form>
		</footer>
  </div><!--contaner-->
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/messenger.js"></script>
  </body>
</html>
