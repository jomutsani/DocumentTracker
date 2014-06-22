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
<section class="ui-field-contain, ui-corner-all custom-corners">
  <header class="ui-bar ui-bar-a"><h1>Login</h1></header>
  <article class="ui-body ui-body-a">
    <form action="./?page=login" method="post">
      <div class="ui-field-contain">
        <label for="uid">ID Number</label>
        <input type="text" name="uid" id="uid"/>
      </div>
      <div class="ui-field-contain">
        <label for="password">Password</label>
        <input type="password" name="password" id="password"/>
      </div>
      <div class="ui-field-contain">
        <input type="hidden" name="lasturl" value="<?php echo urlencode(curPageURL()); ?>"/>
        <input type="submit" value="Login" data-icon="forward"/>
      </div>
    </form>
  </article>
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document Tracker</title>
    <link rel="stylesheet" href="./css/jquery.mobile.structure-1.4.2.min.css" />
    <link rel="stylesheet" href="./css/jquery.mobile.theme-1.4.2.min.css" />
    <link rel="stylesheet" href="./css/jquery.mobile-1.4.2.min.css" />
    <link rel="stylesheet" href="./css/jquery.mobile.external-png-1.4.2.min.css" />
    <link rel="stylesheet" href="./css/jquery.mobile.icons-1.4.2.min.css" />
    <link rel="stylesheet" href="./css/jquery.mobile.inline-png-1.4.2.min.css" />
    <link rel="stylesheet" href="./css/jquery.mobile.inline-svg-1.4.2.min.css" />
    <link rel="stylesheet" href="./css/default.css" />
    <script src="./js/jquery-2.1.1.min.js"></script>
    <script src="./js/jquery.mobile-1.4.2.min.js"></script>
  </head>
  <body>
    <div data-role="page">
    <header data-role="header"><hgroup><h1>Document Tracker</h1></hgroup></header>
    <div role="main" class="ui-content">
<?php
displayNotification();
}

function getHTMLPageFooter()
{
?>
    </div>
    <footer data-role="footer">a</footer>
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
