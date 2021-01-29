<?php
  session_start();
  include("top.html")
?>
    <script src="login.js" type="text/javascript"></script>
  </head>
  <body>

    <div id="errMsg">
      <?php
        if(isset($_SESSION["msg"]))
        {
          echo $_SESSION["msg"]; 
          unset($_SESSION["msg"]); 
        }
        else
        {
          echo "Successfully logged out.";
        }
      ?>
    </div>

    <fieldset id="login">
      <legend>Log-in</legend>
      <div>
        Name : <input type="text" name="name"><br>
        Password : <input type="password" name="password"><br>
        <input type="submit" value="Log in" id="submitLogin"><br>
      </div>
    </fieldset>

    <div id="w3c">
      <a href="http://validator.w3.org/check/referer"> 
        <img width="88" src="https://upload.wikimedia.org/wikipedia/commons/b/bb/W3C_HTML5_certified.png" alt="Valid HTML5!">
      </a>
      <a href="http://jigsaw.w3.org/css-validator/check/referer">
        <img src="http://jigsaw.w3.org/css-validator/images/vcss" alt="Valid CSS" >
      </a>
    </div>
	</body>
</html>
