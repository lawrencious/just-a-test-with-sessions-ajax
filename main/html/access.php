<?php
      session_start();
      include("login_supp.php");
      $name = $_POST["name"];
      $password = $_POST["password"];
      $json = "{ \"href\" : ";

      $result = is_password_correct($name, $password);
      if($result)
      {
        $_SESSION["name"] = strtoupper($name);
        $json .= "\"index.php\" }";
      }
      else
      {
        $_SESSION["msg"] = "Username and/or password do not match or are incorrect: please try again.";
        $json .= "\"login.php\" }";
      }
      
      echo $json;
?>