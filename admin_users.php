<?php
		include 'init_session.php';
		init_admin();
		include 'connect.php';
		if(isset($_GET['del_user_id'])){
			$del_user_id = $con->real_escape_string($_GET['del_user_id']);
			$con->query(
				"DELETE FROM user WHERE user_id={$del_user_id}"
			);
			$con->query(
				"DELETE FROM message WHERE from_user_id={$del_user_id} OR to_user_id={$del_user_id}"
			);
		}
		if(isset($_GET['block_user_id'])){
			$con->query(
				"UPDATE user SET use_room_id=NULL, switch_date=CURRENT_TIMESTAMP, user_blocked=1"
				." WHERE user_id={$con->real_escape_string($_GET['block_user_id'])}"
			);
		}
		if(isset($_GET['unblock_user_id'])){
			$con->query(
				"UPDATE user SET user_blocked=0"
				." WHERE user_id={$con->real_escape_string($_GET['unblock_user_id'])}"
			);
		}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Чат - Админ панель - Пользователи</title>
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="css/bootstrap.min.css">
		<style>
			body{
				padding-top: 10px;
			}
			table {
				width: 90%;
				font-size: 14px;
				font-weight: normal;
			}
			th {
				font-weight: bold;
			}
			tr, th {
				height: 50px;
			}
			.delete-control{
			  display: inline-block;
			  text-align: center;
			  width:15px;
			  height: 15px;
			  background-color: red;
			  opacity: 0.4;
			  color: white;
			  font-size: 11px;
			  -webkit-border-radius: 7px;
			  -moz-border-radius: 7px;
			  -o-border-radius: 7px;
			  border-radius: 7px;
			  margin: 0 0 0 7px;
				vertical-align: middle;
			}
			.delete-control:hover{
			  opacity: 1;
				text-decoration: none;
				color:white;
			}
		</style>
  </head>
  <body>
  <div class="container">
    <div class="row">
			<?php
				insert_admin_menu();
				insert_logout_control();
			?>
    </div>
			<h1>Чат - Админ панель - Пользователи</h1>
			<?php
				//ВСЕ ПОЛЬЗОВАТЕЛИ КРОМЕ АДМИНА
				$r = $con->query("SELECT user.*, room.room_name FROM user
													LEFT JOIN room ON user.use_room_id=room.room_id
													WHERE user.user_type <> 1 ORDER BY user.user_reg_date DESC");
				echo '
					<table class="table"><tbody>
						<tr style="font-weight: bold;">
							<td>имя</td>
							<td>логин</td>
							<td>сейчас в комнате</td>
							<td>статус</td>
							<td>зарегистрирован</td>
							<td>блокировка</td>
						</tr>';
				if($con->affected_rows > 0)
				{
						function status_filter($room_name){
							if(strlen($room_name)>0){
								return '<span class="label label-success">&nbsp;ЗАШЕЛ&nbsp;</span>';
							}
							return '<span class="label label-default">&nbsp;ВЫШЕЛ&nbsp;</span>';
						}
						function bloking_filter($user_id, $user_blocked){
							if($user_blocked==1)
								return '<a href="admin_users.php?unblock_user_id='.$user_id
								.'" title="СНЯТЬ БЛОКИРОВКУ" class="label label-default">&nbsp;БЛОКИРОВАН&nbsp;</a>';
							else
							 	return '<a href="admin_users.php?block_user_id='.$user_id
								.'" title="ЗАБЛОКИРОВАТЬ ПОЛЬЗОВАТЕЛЯ" class="label label-success">&nbsp;РАЗРЕШЕН&nbsp;</a>';
						}


						while ($row = $r->fetch_assoc()) {
							echo "
							<tr>
								<td><b>{$row['user_name']}</b></td>
								<td>{$row['user_login']}</td>
								<td>{$row['room_name']}</td>
								<td>".status_filter($row['room_name']).' '
									.date('d.m.y H:i',strtotime($row['switch_date'])).'</td>'
							.'<td>'.date('d.m.y H:i',strtotime($row['user_reg_date']))
								.'<a href="admin_users.php?del_user_id='.$row['user_id']
								.'" class="delete-control" title="УДАЛИТЬ ПОЛЬЗОВАТЕЛЯ И ЕГО СООБЩЕНИЯ">X</a></td>'
							.'<td>'.bloking_filter($row['user_id'], $row['user_blocked']).'</td>
							</tr>';
				    }
				    $r->free();
				}
				echo '</tbody></table>';
		?>
  </div>

  </body>
</html>
