<?php
  /**
   *  Queries the database to found if a user with
   *  username $name actually exists and, if so, if 
   *  the given password is the one associated to $name
   **/
  function is_password_correct($name, $password)
  {
    $dbconnstr = "mysql:dbname=imdb_small; host=localhost:3306";
    $db = new PDO($dbconnstr, "root", "");

    $name = $db->quote(strtolower($name));
    $query = "SELECT DISTINCT password
              FROM users
              WHERE LOWER(username) = $name;";
    
    $rows = $db->query($query);
    if($rows)
    {
      $rows = $rows->fetchAll();
      foreach($rows as $row)
      {
        return strcmp($row["password"], $password) == 0;
      }
    }
    else
    {
      return FALSE;
    }
  }

  /**
   *  When index.php page is opened, checks
   *  if a valid session is still open
   **/
  function ensure_login()
  {
    if(!isset($_SESSION["name"]))
    {
      redirect("login.php", "Session expired: please log back in.");
    }
  }

  /**
   *  If the session is expired, it redirects to
   *  the login page
   **/
  function redirect($url, $msg)
  {
    if(isset($_SESSION))
    {
      $_SESSION["msg"] = $msg;
    }
    header("Location: $url");
    die;
  }
?>