<?php
//Define Conn Properties
$conn;
define('DT_NOTIF_NORMAL', 0);
define('DT_NOTIF_WARNING', 1);
define('DT_NOTIF_ERROR', 2);
define('DT_DB_SERVER', 'localhost');
define('DT_DB_USER', "root");
define('DT_DB_PASSWORD', "P@ssw00rd");
define('DT_DB_NAME', "documenttracker");

function getLoginPage()
{
?>
<section>
  <header><h1>Login</h1></header>
  <div>
    <form action="./?page=login" method="post">
      <ul>
        <li>
          <label for="uid">ID Number</label>
          <input type="text" name="uid" id="uid"/>
        </li>
        <li>
          <label for="password">Password</label>
          <input type="password" name="password" id="password"/>
        </li>
        <li>
          <input type="hidden" name="lasturl" value="<?php echo urlencode(curPageURL()); ?>"/>
          <input type="submit"/>
        </li>
      </ul>
    </form>
  </div>
</section>
<?php
}

function getHTMLPageHeader()
{
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Document Tracker</title>
  </head>
  <body>
    <header><hgroup><h1>Document Tracker</h1></hgroup></header>
    <div>
<?php
displayNotification();
}

function getHTMLPageFooter()
{
?>
    </div>
  </body>
</html>      
<?php
}

function dbConnect(){
  global $conn;
  $conn=new mysqli(DT_DB_SERVER, DT_DB_USER, DT_DB_PASSWORD, DT_DB_NAME);
  if($conn->connect_error)
  {
    trigger_error("<p><strong>Database connection failed<strong></p>".$conn->connect_error, E_USER_ERROR);
  }
}

function dbClose()
{
  global $conn;
  $conn->close();
}

function curPageURL() {
  //$pageURL = 'http';
  //if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL = "//";
  if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
  } else {
    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
  }
  return $pageURL;
}

function setNotification($msg, $type=DT_NOTIF_NORMAL)
{
  setcookie("notifmsg", $msg);
  setcookie("notiftype",$type);
}

function displayNotification()
{
  if(isset($_COOKIE['notifmsg']) && isset($_COOKIE['notiftype']))
  {
?>
<aside id="notif" class="notification, notif<?php echo $_COOKIE['notiftype']; ?>">
  <p><?php echo $_COOKIE['notifmsg']; ?></p>
</aside>
<?php
  setcookie("notifmsg",null,time()-3600);
  setcookie("notiftype",null,time()-3600);
  }
}
?>
