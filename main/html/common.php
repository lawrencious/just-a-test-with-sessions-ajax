<?php
  /**
   *  Connects to the database.
   *  In case of success, returns the PDO object associated
   *    to it.
   *  Otherwise, exits the programme with failure, printing
   *    the occured error.
   **/
  function connectDB()
  {
    $dbconnstr = "mysql:dbname=imdb_small; host=localhost:3306";
    $db = NULL;
    try
    {
      $db = new PDO($dbconnstr, "root", "");
    }
    catch (PDOException $ex)
    {
      echo "Couldn't establish connection to database.\n";
      exit($ex->getMessage());
    }

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); #enables exceptions
    return $db;
  }

  /**
   *  Reads the data given by the user and prepares
   *    them for using them in a query.
   *  If actor exists in $db, it will return said values in 
   *    an array.
   *  Otherwise, NULL.
   **/
  function readUserInput($arr, $db)
  {
    $input = [
      "firstName" => $db->quote($arr["firstname"]."%"),
      "lastName" => $db->quote($arr["lastname"]),
      "all" => $arr["all"]
    ];

    if(exists($db, $input["firstName"], $input["lastName"]))
      return $input;
    else
      return NULL;
  }

  /**
   *  Checks whether actor with first name $firstName
   *    and last name $lastName exists in $db.
   **/
  function exists($db, $firstName, $lastName)
  {
    $query = "SELECT id
              FROM actors
              WHERE first_name LIKE $firstName AND
                    last_name = $lastName;";

    return count($db->query($query)->fetchAll()) != 0;
  }

  /**
   *  Defines the query that finds the actor with first name
   *    similar to $firstName and last name equal to $lastName.
   **/
  function searchActorQuery($firstName, $lastName)
  {
    return "(SELECT r1.actor_id id, COUNT(*) count
             FROM actors a2, movies m1, roles r1
             WHERE a2.id = r1.actor_id AND
                   m1.id = r1.movie_id AND
                   a2.first_name LIKE $firstName AND 
                   a2.last_name = $lastName
             GROUP BY r1.actor_id
             ORDER BY count DESC, r1.actor_id
             LIMIT 1) T";
  }


  /**
   *  Defines the query which, by default, selects
   *    only the films where the actor/actress chosen
   *    by the user had starred.
   *  If all is set, then intersects these films with
   *    the ones in which Kevin Bacon had starred.
   **/
  function defineQuery($input)
  {
    $firstName = $input["firstName"];
    $lastName = $input["lastName"];
    $all = $input["all"];

    if(strcmp($all, "true") != 0)
    {
      $query = "SELECT DISTINCT T1.name name, T1.year year
                FROM (SELECT DISTINCT m.id, m.name, m.year
                      FROM roles r, movies m, actors a
                      WHERE r.movie_id = m.id AND
                            r.actor_id = a.id AND
                            a.id = (SELECT DISTINCT a1.id
                                    FROM actors a1, ".searchActorQuery($firstName, $lastName)."
                                    WHERE a1.id = T.id)) T1
                INNER JOIN (SELECT DISTINCT m.id, m.name, m.year
                            FROM roles r, movies m, actors a
                            WHERE r.movie_id = m.id AND
                                  r.actor_id = a.id AND
                                  a.id = (SELECT DISTINCT a1.id
                                          FROM actors a1, ".searchActorQuery("'Kevin%'", "'Bacon'")."
                                          WHERE a1.id = T.id)) T2
                ON T1.id = T2.id
                GROUP BY year
                ORDER BY year DESC, name;";
    }
    else
    {
      $query = "SELECT DISTINCT m.name name, m.year year
                FROM movies m, actors a, roles r
                WHERE m.id = r.movie_id AND
                      a.id = r.actor_id AND
                      a.id = (SELECT DISTINCT a1.id
                              FROM actors a1, ".searchActorQuery($firstName, $lastName)."
                              WHERE a1.id = T.id)
                GROUP BY year
                ORDER BY year DESC, name";
    }

    return $query;
  }


  /**
   *  Executes $query in $db.
   *  If successful, returns the data selected
   *    by $query.
   *  Otherwise, exits the programme with failure, printing
   *    the occurred error.
   **/
  function fetchData($db, $query)
  {
    $rows = NULL;
    try
    {
      $rows = $db->query($query)->fetchAll();
    }
    catch (PDOException $ex)
    {
      echo "Couldn't execute SELECT statement.\n";
      exit($ex->getMessage());
    }

    return $rows;
  }


  /**
   *  If the query was successful, it organises the
   *    data that were found in a JSON object, having
   *    the attribute "movieList" in which all the films
   *    are stored as {#row, name, year} object.
   *  Otherwise, returns an empty object.
   **/
  function analyseData($rows)
  {
    $i = 1;
    if($rows != FALSE && count($rows) > 0)
    {
      $json = "{\n\t\"movieList\":\n\t\t[\n";
      foreach($rows as $row)
      {
        $name = $row["name"];
        $year = $row["year"];
        if($name[0] == '"')
        {
          $name = substr($name, 1, -1);
        }
        $json .= "    {\n\t\"id\":\"$i\",\n\t\"name\":\"$name\",\n\t\"year\":\"$year\"\n\t}";
        if($i < count($rows))
        {
          $i++;
          $json .= ",";
        }
        $json .= "\n";
      }
      $json .= "  ]\n}\n";

      return $json;
    }

    return "{}";
  }
?>