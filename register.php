<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Чат. Регистрация пользователя</title>
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/pretty_border.css">
		<style>
      body{
        background-color: #9af;
        padding-top: 10px;
      }
      h1 {
        text-align: center;
      }
			form {
				font-size: 18px;
				width: 430px;
				padding: 10px 15px;
				border: 1px solid blue;
        margin: 5vh auto;
        background-color: rgba(255,255,255,0.8);
			}
			label {
				display:block;
				height: 40px;
				margin: 5px 10px;
        padding-top: 5px;
			}
      .error-message, #good-login {
        display: none;
        width: 50%;
        margin: 0 10% 0 auto;
        padding: 0;
        text-align: right;
        color: red;
        font-weight: bold;
        font-size: 14px;
      }
      #good-login{
        color: green;
        font-size: 12px;
      }
		</style>
  </head>
  <body>
  <div class="container">
		<h1>Чат. Регистрация пользователя</h1>
			<form action="register_user.php" method="POST" class="form-inline pretty_border" onSubmit="return SubmitForm();">
			<label>Ваше имя
        <input class="form-control pull-right" type="text" name="name" minlength=10 maxlength="30"
          placeholder="10-30 букв кириллицей" title="10-30 букв кириллицей" pattern="[А-Яа-яЁё \-]+"
          required autofocus />
      </label>
			<label>Логин
        <input class="form-control pull-right" type="text" name="login" minlength=5 maxlength="20"
          placeholder="5-20 букв латиницей" title="5-20 букв латиницей" pattern="[A-Za-z\.\-]+"
          required onBlur="CheckLogin();"/>
      </label>
      <div id="login-error" class="error-message">такой логин уже занят</div>
      <div id="good-login">такой логин доступен</div>
			<label>Пароль
				<input class="form-control pull-right" type="password" name="pass"
          minlength=5 required title="минимум 5 символов"/>
			</label>
      <label>повторите
				<input class="form-control pull-right" type="password" name="pass2"
          minlength=5 required />
			</label>
      <div id="pass-error" class="error-message"></div>
			<div style="text-align:center; margin:20px 0 10px 0;">
				<input id="submit" class="btn btn-success" type="submit" value="Готово" />
				<a href="index.php" class="btn btn-info">Я передумал<a/>
			</div>
		</form>
  </div>
	<script>
    var good_login = false;
    function show_pass_error(text){
      document.getElementById('pass-error').style.display='block';
      document.getElementById('pass-error').innerHTML=text;
    }
    function SubmitForm(){
      if(!good_login){
        CheckLogin();
        return false;
      }
      var pass1 = document.getElementsByName('pass')[0].value;
      if(pass1.length<8){
        show_pass_error('минимум 8 символов в пароле');
        return false;
      }
      if( pass1 != document.getElementsByName('pass2')[0].value){
          show_pass_error('пароль не совпадает');
          return false;
        }
      return true;
    }

    function CheckLogin(){
      document.getElementById('login-error').style.display='none';
      var login = document.getElementsByName('login')[0].value;
      good_login = false;
      if(login.length<8) return false;
      xhr.open('GET', 'check_login.php?login='+encodeURI(), true);
      xhr.send();
      xhr.onreadystatechange = function() {
        if (xhr.readyState != 4) return;
        if (xhr.status != 200) {
          console.log(xhr.status + ': ' + xhr.statusText);
        } else {
          if(xhr.responseText=='1'){
              good_login = true;
              document.getElementById('good-login').style.display='block';
              document.getElementById('login-error').style.display='none';
          }
          else{
              document.getElementById('login-error').style.display='block';
              document.getElementById('good-login').style.display='none';
          }
        }
      }

    }

    var xhr;
    try {
      xhr = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      try {
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (E) {
        xhr = false;
      }
    }
    if (!xhr && typeof XMLHttpRequest!='undefined') {
      xhr = new XMLHttpRequest();
    }
	</script>
  </body>
</html>
