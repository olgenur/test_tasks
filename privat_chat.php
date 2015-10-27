<?php
		include 'init_session.php';
		if(!isset($_GET['uid']))
				redirect_to('index.php');
		$user_id = $_GET['uid'];
		$user_name = $_GET['un'];
		$room_id = $_GET['rid'];
		$room_name = $_GET['rn'];
		init_user();
		include 'connect.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Приватный чат - <?php echo $user_name; ?></title>
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    <link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/messenger.css">
		<link rel="stylesheet" href="css/pretty_border.css">
	 </head>

  <body>
  <div class="container">
			<header class="row pretty_border">
				<?php
					if($local_user['type']==1) insert_admin_menu();
					insert_logout_control();
					echo '<script>'
								.'var last_date = '.time()
								.', my_id = '.$_SESSION[$key_name]['id']
								.', to_user_id = '.$user_id
								.', uid_list = []'
								.', to_user_name = "'.$user_name.'"'
							.';</script>';
				?>
				<h1>
					<a href="room_chat.php?room_id=<?php echo $room_id; ?>">
						<?php echo $room_name; ?>
					</a> - приватный чат - <b><?php echo $user_name; ?></b>
				</h1>
			</header>

			<div class="row">
				<div class="messages-list pretty_border" style="width:100%; display:block;"><!--блок сообщений-->
					<div class="content">
						<dl>
							<?php
								$my_id = $_SESSION[$key_name]['id'];
								$r = $con->query(
									"SELECT mes_id, mes_text, mes_date, from_user_id FROM `message` WHERE type_id = 0 AND "
									."((to_user_id = ".num_esc($my_id)." AND from_user_id = ".num_esc($user_id).") OR"
									." (to_user_id = ".num_esc($user_id)." AND from_user_id = ".num_esc($my_id)."))"
								);
								while ($msg = $r->fetch_assoc()) {
									if($msg['from_user_id']==$my_id){
										echo '<dt class="my-message">'.date('d.m.y H:i',strtotime($msg['mes_date'])).' - Я </dt>';
										echo '<dd class="my-message">'.$msg['mes_text'].'</dd>';
									}
									else{
										echo '<dt>'.date('d.m.y H:i',strtotime($msg['mes_date'])).' - '.$user_name.'</dt>';
										echo '<dd>'.$msg['mes_text'].'</dd>';
									}
								}
								$r->free();
							?>
					</dl>
				</div>
			</div><!--блок сообщений-->

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
