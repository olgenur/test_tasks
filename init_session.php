<?php
error_reporting(E_ALL);
session_start();
$key_name = 'chat';
$local_user = [];

function get_user_token($id){
    return md5('qtYuf&756JP8!%hgk8*5fjhv67Gh'.$id.date('dmy'));
}

function redirect_to($url){
  header("Location: ".$url);
  exit();
}

function init_user(){
  global $key_name, $local_user, $room_id;
  if(isset($_SESSION[$key_name])){
    $local_user = $_SESSION[$key_name];
    if($local_user['token'] != get_user_token($local_user['id']))
        redirect_to('login.php?room_id='.$room_id);
  }
  else{
    redirect_to('login.php?room_id='.$room_id);
  }
}

function init_admin(){
  global $key_name, $local_user;
  if(isset($_SESSION[$key_name])){
    $local_user = $_SESSION[$key_name];
    if($local_user['token'] != get_user_token($local_user['id']) || $local_user['type'] != 1)
        redirect_to('login.php');
  }
  else{
    redirect_to('login.php');
  }
}

function insert_logout_control(){
  global $local_user;
  echo '<span class="pull-right logout-control">'.$local_user['name']
  .' <a href="index.php?logout=1" class="btn btn-warning">Выйти</a></span>';
}

function insert_admin_menu(){
  echo '<style>.btn{margin:2px 4px;}</style><a href="index.php" class="btn btn-primary">Комнаты</a>'
      .'<a href="admin_messenger.php" class="btn btn-primary">Лента сообщений</a>'
      .'<a href="admin_users.php" class="btn btn-primary">Пользователи</a>';
}
?>
