<?php
require_once 'functions.php';
session_start();
$_GET['page']=(isset($_GET['page'])?$_GET['page']:"dashboard");
if(isset($_SESSION['uid'])):
  dbConnect();
  switch ($_GET['page']):
  case 'logout':
    session_destroy();
    setNotification("Successfully logged out.");
    header("Location: ./");
    break;

  case 'add':
      getHTMLPageHeader();
?>
    <header><h1>Add Document</h1></header>
    <article>
    <form action="./adddoc" method="post" data-ajax="false">
        <label for="documentnumber">Document Number</label>
        <input type="text" name="documentnumber" id="documentnumber"/>

        <label for="remarks">Remarks</label>
        <input type="text" name="remarks" id="remarks"/>

        <input type="submit" value="Add" data-icon="plus" data-ajax="false"/>

    </form>
    </article>
<?php
    getHTMLPageFooter();
    break;

  case 'adddoc':
        global $conn;
        $stmt=$conn->prepare("INSERT INTO document(documentnumber,remarks,author) VALUES(?,?,?)");
        if($stmt === false) {
          trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
        }
        $userid=(isset($_SESSION['id'])?$_SESSION['id']:0);
        $stmt->bind_param('ssi',$_POST['documentnumber'],$_POST['remarks'],$userid);
        $stmt->execute();
        $trackno = $stmt->insert_id;
        
        $stmt=$conn->prepare("INSERT INTO documentlog(trackingnumber,remarks,user) VALUES(?,?,?)");
        if($stmt === false) {
          trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
        }
        $userid=(isset($_SESSION['id'])?$_SESSION['id']:0);
        $stmt->bind_param('isi',$trackno,"Document received at ".$_SESSION['department']." (".$_SESSION['section']."). Document Remarks: ".$_POST['remarks'],$userid);
        $stmt->execute();
        
        writeLog("Document ".$trackno." has been added by ".$_SESSION['fullname']."(".$_SESSION['uid'].").");
        header("Location: ./");
        break;
?>
<?php
    default :
      getHTMLPageHeader(); 
      echo "<h1>".$_SERVER["QUERY_STRING"]."</h1>";
?>

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
