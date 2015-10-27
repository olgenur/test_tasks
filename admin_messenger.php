<?php
		include 'init_session.php';
		init_admin();
		include 'connect.php';

		if(isset($_POST['message']) || isset($_POST['last_date'])){

				header("Content-type: text/plain; charset=utf8");
				header("Cache-control: no-store, no-cache, must-revalidate");
				header("Cache-control: post-check=0, pre-check=0", false);
				header("Pragma: no-cache");

				if(isset($_POST['message'])){
					$msg = strip_tags($_POST['message'],'<b><i><u><pre>');
					$query_data = 'mes_date=CURRENT_TIMESTAMP, mes_text='.str_esc($msg)
											.', from_user_id='.num_esc($_SESSION[$key_name]['id'])
											.', to_user_id=NULL, room_id=NULL, type_id=2';
					$con->query('INSERT INTO message SET '.$query_data);
					echo json_encode(array('answer' => 1));
					die();
				}

				if(isset($_POST['last_date'])){
					$last_date = $_POST['last_date'];

					$msg_array = array();
					$select = "m.mes_id, m.mes_text, m.mes_date, m.from_user_id, m.type_id,"
							." u.user_name, r.room_id, r.room_name FROM `message` m, `user` u, `room` r";
					$where = "m.type_id > 0 AND m.from_user_id = u.user_id AND "
							."(m.room_id=r.room_id OR m.room_id IS NULL) AND UNIX_TIMESTAMP(mes_date)>"
							.num_esc($last_date);
					$r = $con->query(
						"SELECT {$select} WHERE {$where} GROUP BY m.mes_id ORDER BY m.mes_date"
					);
					if($con->affected_rows>0){
						while ($m = $r->fetch_assoc()) {
							array_push($msg_array, $m);
						}
						$r->free();
						echo json_encode(array(
							'zero' => false, 'msgs' => $msg_array, 'last' => time()
						));
					}
					else{
						echo json_encode(array( 'zero' => true,'last' => time() ));
					}
					die();
				}

		} // END if(isset($_POST['message']) && isset($_POST['message']))

		if(isset($_GET['clear_history'])){
			$con->query("DELETE FROM message WHERE UNIX_TIMESTAMP(mes_date)<".num_esc(time()-2000));
			$con->query("DELETE FROM del_msg_log");
		}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Админ панель. Все сообщения.</title>
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    <link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/admin_messenger.css">
		<link rel="stylesheet" href="css/pretty_border.css">
	 </head>

  <body>
  <div class="container">
			<header class="row pretty_border">
				<?php
					insert_admin_menu();
					echo '<a href="admin_messenger.php?clear_history" class="btn btn-danger">'
								.'ОЧИСТИТЬ ИСТОРИЮ</a>';
					insert_logout_control();
					echo "<script>
									var last_date = ".time().";
								</script>";
					?>
					<h1>АДМИН ПАНЕЛЬ - ВСЕ СООБЩЕНИЯ</h1>
			</header>

			<div class="row">
				<div class="messages-list pretty_border" style="width:100%; display:block;"><!--блок сообщений-->
					<div class="content">
						<dl>
							<?php
								$r = $con->query(
			"SELECT m.mes_id, m.mes_text, m.mes_date, m.from_user_id, m.type_id, u.user_name, r.room_id, r.room_name
			FROM `message` m, `user` u, `room` r
			WHERE m.type_id > 0 AND m.from_user_id = u.user_id AND (m.room_id=r.room_id OR m.room_id IS NULL)
			GROUP BY m.mes_id ORDER BY m.mes_date"
								);

								while ($msg = $r->fetch_assoc()) {
										echo '<dt>'.$msg['room_name'].' - '.date('d.m.y H:i',strtotime($msg['mes_date']))
											.' - '.$msg['user_name'];
										if($msg['type_id']==2) echo ' <span class="mestype_common">ВСЕМ</span>';
										echo '</dt><dd class="with-admin-controls" id="mes'.$msg['mes_id'].'">'
												.'<div class="delete-control">X</div>'.$msg['mes_text'].'</dd>';
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
					<input type="submit" class="btn btn-warning col-md-3" value="ОТПРАВИТЬ ВСЕМ" />
				</div>
			</form>
		</footer>
  </div><!--contaner-->
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/admin_messenger.js"></script>
  </body>
</html>
