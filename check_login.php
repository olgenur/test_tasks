<?php
if(!isset($_GET['login'])){
    echo 0;
    die();
}
include 'connect.php';
$r = $con->query(
  "SELECT user_login FROM user WHERE user_login=".str_esc($_GET['login'])
);
if($con->affected_rows > 0)
{
  echo 0;
}
else{
  echo 1;
}
?>
