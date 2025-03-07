<?php
session_start();

echo 'Welcome to page #1<br />';

echo('PHPSESSID: ' . session_id());

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Swings Budgeter login form</title>
    <link rel="stylesheet" type="text/css" href="CSScode.css" />
  </head>

  <body>
    <div id="form">
      <h1>LOGIN </h1>
      <form name="form" action="verifyLogin.php" method="POST">
        <p>
          <label> USER NAME: </label>
          <input type="text" id="user" name="userid" />
        </p>

        <p>
          <label> PASSWORD: </label>
          <input type="text" id="pass" name="password" />
        </p>

        <p>
          <input type="submit" id="button" value="Login" />
        </p>
</form>
        <h1>Or Register with a username and password</h1>
        <form name="form" action="register.php" method="POST">
        <p>
          <label> USER NAME: </label>
          <input type="text" id="user" name="userid" />
        </p>

        <p>
          <label> PASSWORD: </label>
          <input type="text" id="pass" name="password" />
        </p>
        <p>
          <label> Please re-type PASSWORD: </label>
          <input type="text" id="pass" name="password2" />
        </p>
        <p>
          <input type="submit" id="button" value="Login" />
        </p>


      </form>
    </div>
  </body>
</html>