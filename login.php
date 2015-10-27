<?php
$badlogin = false;
if(isset($_POST['login']))
{
	include 'connect.php';
  $login = str_esc_post('login');
  $pass = $con->real_escape_string(md5($_POST['pass']));
  $badlogin = true;
	$r = $con->query("SELECT * FROM user WHERE "
						."user_login={$login} AND user_pass_md5='{$pass}' AND user_blocked=0");
  if(isset($_POST['room_id'])) $room_id = $_POST['room_id'];
  else $room_id=0;
	if($con->affected_rows > 0)
	{ //REGISTERED USER
		$row = $r->fetch_assoc();
		include 'init_session.php';
    $user_id = $row['user_id'];
		$_SESSION[$key_name] = array(
			'id' => $user_id,
			'type' => $row['user_type'],
      'token' => get_user_token($user_id),
			'name' => $row['user_name'],
      'room_id' => $room_id
		);
    if($room_id>0) redirect_to('room_chat.php?room_id='.$room_id);
		else redirect_to('index.php');
	}
}
if(isset($_GET['room_id'])) $room_id = $_GET['room_id'];
else $room_id=0;
?>

<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Chat. Login page</title>
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    <link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/pretty_border.css">
    <style>
    body {
      margin-top: 30vh;
      margin-left: 30vw;
			background-color: #9af;
    }
    form {
      font-size: 18px;
      width: 40vw;
			max-width: 400px;
			min-width: 370px;
      padding: 10px;
      border: 1px solid blue;
			background-color: rgba(255,255,255,0.8);
    }
    label {
      width: 100%;
      text-align: left;
    }
    </style>
  </head>
  <body>

    <form action="login.php" method="POST" class="form-inline pretty_border">
        <?php
          if($badlogin)
            echo '<p class="text-center text-danger bg-warning">СБОЙ АВТОРИЗАЦИИ!</p>';
          echo '<input type="hidden" name="room_id" value="'.$room_id.'" />';
        ?>
        <label>Логин
					<input class="form-control pull-right" type="text" name="login" placeholder="логин"
					required minlength="5" title="логин, 5-20 букв латиницей" pattern="[A-Za-z\.\-]+"
					autofocus />
				</label><br />
        <label>Пароль
					<input class="form-control pull-right" type="password" name="pass"
					required minlength="5" title="пароль, минимум 5 символов" />
				</label><br />

        <br /><center>
          <input type="submit" class="btn btn-success" value="Войти"/>
          <a class="btn btn-primary" href="register.php">Регистрироваться</a>
          <a href="index.php" class="btn btn-default">На главную</a>
        </center>
    </form>

  </body>
</html>
