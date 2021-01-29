<?php
  session_start();
  session_unset();
  session_destroy();
  $json = "{ \"href\" : \"login.php\",\n";
  $json .= " \"msg\" : \"Successfully logged out. See you soon!\" }";
  echo $json;
?>