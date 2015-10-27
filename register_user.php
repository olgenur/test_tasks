<?php
	if(!isset($_POST['name']) || !isset($_POST['login']) || !isset($_POST['pass'])){
		header("Location: register.php");
		exit();
	}
include 'connect.php';

$newdata = 'user_name='.str_esc_post('name')
				.', user_login='.str_esc_post('login')
				.", user_pass_md5='".$con->real_escape_string(md5($_POST['pass']))."'";
$con->query("INSERT IGNORE INTO user SET {$newdata}");
if($con->affected_rows > 0)
	header("Location: login.php");
else
	header("Location: register.php");
?>
