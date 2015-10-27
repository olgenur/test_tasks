<?php
  include 'init_session.php';
  include 'connect.php';
  $admin = false;
  if(isset($_SESSION[$key_name])){ // SESSION OPEN
    $local_user = $_SESSION[$key_name];
    $loged_in = ($local_user['token'] == get_user_token($local_user['id']));
    if(!$loged_in){
        unset($_SESSION[$key_name]);
    }
    else{
      if(isset($_GET['logout']) || isset($_GET['leave_room'])){
        $con->query("UPDATE user SET use_room_id=NULL, switch_date=CURRENT_TIMESTAMP"
                  ." WHERE user_id=".num_esc($local_user['id']));
        if(isset($_GET['logout'])){
          unset($_SESSION[$key_name]);
          $loged_in=false;
        }
      }
      $admin = ($local_user['type']==1 && $loged_in);
    }
  }
  else{ // SESSION CLOSED
    $loged_in=false;
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Чат. Список комнат.</title>
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/main_page.css">
    <link rel="stylesheet" href="css/pretty_border.css">
  </head>
  <body>
    <div class="container">
      <header class="row">
      <?php
            if($loged_in){
              if($admin) insert_admin_menu();
              insert_logout_control();
            }
            $r = $con->query("SELECT room_id, room_name, room_desc FROM room");
      ?>
      </header>
      <h1>Добро пожаловать в наш чат!</h1>
      <div class="row ">
        <div class="col-md-offset-1 col-md-10 rooms-container pretty_border">
      <?php if(!$loged_in){ ?>
            <form action="login.php" method="POST" class="form-inline login-form">
                <input class="form-control" type="text" name="login" placeholder="логин"
                required minlength="5" title="логин, 5-20 букв латиницей" pattern="[A-Za-z\.\-]+" autofocus />
                <input class="form-control" type="password" name="pass"
                required minlength="5" title="пароль, минимум 5 символов"/>
                <input type="submit" class="btn btn-success" value="Войти"/>
                <a class="btn btn-warning" href="register.php">Регистрироваться</a>
            </form>
      <?php  } ?>

          <dl>
          <?php
    				if($con->affected_rows > 0) {
    						while ($row = $r->fetch_assoc()) {
                  $rid = $row['room_id'];
    							echo '<dt id="dt'.$rid.'"><a href="room_chat.php?room_id='.$rid.'">'.$row['room_name'].'</a>';
                  if($admin)
                    echo '
                    <span class="pull-right">
                      <input type="button" class="btn btn-warning" value="редактировать"
                      onClick="editRoom('.$rid.')" />
                      <input type="button" class="btn btn-danger" value="удалить"
                      onClick="deleteRoom('.$rid.')" />
                    </span>';
                  echo '</dt><dd id="dd'.$rid.'">'.$row['room_desc'].'</dd>';
    				    }
    						$r->free();
    				}
            if($admin)
              echo '<input type="button" class="btn btn-success pull-right" '
                  .'value="Создать комнату" onClick="createRoom()" '
                  .'style="margin-bottom:15px;"/>';
    		  ?>
          </dl>

      </div>

    </div>

  <div class="row site-description">
    <p><b>АВТОР САЙТА: ОЛОВЯННИКОВ ГЕННАДИЙ ЮРЬЕВИЧ</b><br/>
    САЙТ РАЗРАБОТАН КАК ТЕСТОВОЕ ЗАДАНИЕ</p>
    <p><b>ТЕКСТ ЗАДАНИЯ:</b></p>
    <p>Написать чат с обновлением в реальном времени (с использованием PHP / MySQL / AJAX).
    Реализовать оформление при помощи Bootstrap (или любого другого известного тебе CSS фреймворка)<p>
    <p>1. Вручную создать трёх пользователей:  двух обычных и одного администратора.<br/>
    1.1. Администратор: может отправлять, удалять и просматривать все сообщения, а также создавать каналы<br/>
    1.2. Пользователь: может видеть только свои, и адресованные ему или всем сообщения, а также отправлять их.</p>
    <p>2. Создать мини-форму авторизации пользователей чата (валидация не обязательна).</p>
    <p>3. В чате должно быть реализовано создание и переключение между каналами , а также приватные переписки. (уточнения по этому пункту:
    Канал - это как отдельная комната (чат-комната) , ветка где общаются отдельные пользователи , плюс реализована возможность общаться 1 на 1.
    Пусть Админ имеет право создавать, удалять и управлять этими каналами.)</p>

    <p>В случае использования PHP фреймворков, рекомендуется реализовать все используя REST API и JSON обмен данными.</p>

    <p>Бонусные задания:<br/>
    1. Релизовать регистрацию новых пользователей.<br/>
    2. Реализовать проверку вводимых данных при регистрации и авторизации.</p>
    <br /><br />
    <p>Для ознакомления Вы можете использовать логин и пароль "spiderman".</p>
    <small>Контактные данные: г.Одесса, olgenur@gmail.com</small>
  </div>

    <?php if($admin){ ?>
    <div id="popup-editor">
      <form action="admin_edit_room.php" method="POST" class="form-inline">
        <input type="hidden" name="room_id"/>
        <input type="hidden" name="action"/>
        <label>Название комнаты <input type="text" class="form-control pull-right"
           name="title" required minlength="10" maxlength="30" title="10-30 символов"
           onChange="$('#submit-room').css('display',(this.value.length>10)?'inline-block':'none');"/></label>
        <label>Описание <input type="text" class="form-control pull-right"
            name="description" required minlength="20" maxlength="60" title="20-60 символов"
            onChange="$('#submit-room').css('display',(this.value.length>20)?'inline-block':'none');"/></label>
        <input type="submit" id="submit-room" class="btn btn-success" value="Сохранить" />
        <input type="submit" id="delete-room" class="btn btn-danger" value="Удалить с перепиской" />
        <input type="button" class="btn btn-warning" value="Отменить"
          onClick="$('#popup-editor').fadeOut()"/>
      </form>
      <script type="text/javascript" src="js/jquery.min.js"></script>
      <script>
        function editRoom(rid){
            var title = $('#dt'+rid).find('a').html(),
                desc = $('#dd'+rid).html();
            var editor=$('#popup-editor');
            editor.find('[name="room_id"]').val(rid);
            editor.find('[name="action"]').val('edit');
            editor.find('[name="title"]').val(title);
            editor.find('[name="title"]').prop('disabled', false);
            editor.find('[name="description"]').val(desc);
            editor.find('[name="description"]').prop('disabled', false);
            $('#submit-room').css('display','none');
            $('#delete-room').css('display','none');
            $('#popup-editor').fadeIn();
            editor.find('[name="title"]').focus();
        }
        function deleteRoom(rid){
            var title = $('#dt'+rid).find('a').html(),
                desc = $('#dd'+rid).html();
            var editor=$('#popup-editor');
            editor.find('[name="room_id"]').val(rid);
            editor.find('[name="action"]').val('delete');
            editor.find('[name="title"]').val(title);
            editor.find('[name="title"]').prop('disabled', true);
            editor.find('[name="description"]').val(desc);
            editor.find('[name="description"]').prop('disabled', true);
            $('#submit-room').css('display','none');
            $('#delete-room').css('display','inline-block');
            $('#popup-editor').fadeIn();
        }
        function createRoom(){
            var editor=$('#popup-editor');
            editor.find('[name="action"]').val('create');
            editor.find('[name="title"]').prop('disabled', false);
            editor.find('[name="description"]').prop('disabled', false);
            editor.find('[name="title"]').val('');
            editor.find('[name="description"]').val('');
            $('#submit-room').css('display','none');
            $('#delete-room').css('display','none');
            $('#popup-editor').fadeIn();
            editor.find('[name="title"]').focus();
        }
      </script>
    </div>
    <?php } ?>

  </body>
</html>
