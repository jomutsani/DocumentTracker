<?php
require_once 'functions.php';
session_start();
if(isset($_SESSION['uid'])):
  dbConnect();
  switch ($_GET['page']):
  case 'login':
    //echo 'login';
    break;
?>


<?php
    default :
      getHTMLPageHeader();
?>
<h1>Welcome <?php echo $_SESSION['fullname']; ?>!</h1>
<?php
      getHTMLPageFooter();
  endswitch;
  dbClose();
elseif((isset($_GET['page'])) && ($_GET['page']=='login')):
  global $conn;
  dbConnect();
  $stmt=$conn->prepare("SELECT id, uid, fullname, department, section FROM user WHERE uid=? AND password=?");
  if($stmt === false) {
    trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
  }
  $stmt->bind_param('is',$_POST['uid'],$_POST['password']);
  $stmt->execute();
  $stmt->store_result();
  echo $stmt->num_rows;
  if($stmt->num_rows==1)
  {
    $stmt->bind_result($_SESSION['id'],$_SESSION['uid'],$_SESSION['fullname'],$_SESSION['department'],$_SESSION['section']);
    while($stmt->fetch()){}
    writeLog($_SESSION['fullname']."(".$_SESSION['uid'].") logged in to the system.");
  }
  else
  {
    setNotification("Wrong ID Number and/or password.",DT_NOTIF_ERROR);
    print_r($_COOKIE);
  }
  $stmt->close();
  dbClose();
  header("Location:".urldecode($_POST['lasturl']));
else:
  getHTMLPageHeader();
?>
<div class="loginpage">
  <div class="ui-block-a">
<?php
  //getLoginPage();
?>
  </div>
  <div class="ui-block-b">
    <div class="ui-body">
      <p>Track and trace the documents at the tip of your fingers.</p>
    </div>
  </div>
</div> 
<?php  
  getHTMLPageFooter();
endif;
?>