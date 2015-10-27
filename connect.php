<?php
$con = mysqli_connect("127.0.0.1", "root", "");
mysqli_select_db($con,"chat") or die(mysql_error());

if ($con->connect_errno) {
    echo "Не удалось подключиться к MySQL: (" . $con->connect_errno . ") " . $con->connect_error;
}
else {
	$con->query('SET NAMES utf8;');
}

function str_esc_post($name){
  global $con;
  return "'".$con->real_escape_string($_POST[$name])."'";
}
function num_esc_post($name){
  global $con;
  return $con->real_escape_string($_POST[$name]);
}

function str_esc($name){
  global $con;
  return "'".$con->real_escape_string($name)."'";
}
function num_esc($name){
  global $con;
  return $con->real_escape_string($name);
}
?>
