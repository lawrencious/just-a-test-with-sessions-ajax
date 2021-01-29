<?php
  include "common.php";

  $db = connectDB();

  $input = readUserInput($_GET, $db);

  if($input == NULL)
  {
    print "{}";
  }
  else
  {
    $query = defineQuery($input);
    $rows = fetchData($db, $query);
    $json = analyseData($rows);
    print $json;
  }
?>