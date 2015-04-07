<?php
//Initialize script
require_once 'functions.php';
session_start();

//If the page is the default page, name it dashboard
$_GET['page']=(isset($_GET['page'])?$_GET['page']:"dashboard");

//If session is active
if(isLoggedIn()):
  dbConnect();  //connect to database

  //Branching logic for page identification
  switch ($_GET['page']):
    
    case 'logout':
      session_destroy();
      setNotification("Successfully logged out.");
      header("Location: ./");
      break;

    case 'add':
      displayHTMLPageHeader();
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
      displayHTMLPageFooter();
      break;

    case 'adddoc':
      global $conn;
      $stmt=$conn->prepare("INSERT INTO document(documentnumber,remarks,author) VALUES(?,?,?)");
      if($stmt === false) {
        trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
      }
      $userid=(isLoggedIn()?$_SESSION['uid']:0);
      $stmt->bind_param('ssi',$_POST['documentnumber'],$_POST['remarks'],$userid);
      $stmt->execute();
      $trackno = $stmt->insert_id;

      $stmt=$conn->prepare("INSERT INTO documentlog(trackingnumber,remarks,user) VALUES(?,?,?)");
      if($stmt === false) {
        trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
      }
      $userid=(isLoggedIn()?$_SESSION['uid']:0);
      $msgremarks="Document received at ".$_SESSION['department']." (".$_SESSION['section']."). Document Remarks: ".$_POST['remarks'];
      $stmt->bind_param('isi',$trackno,$msgremarks,$userid);
      $stmt->execute();

      setNotification("Document was successfully added. Tracking number is <strong>".str_pad($trackno,8,"0",STR_PAD_LEFT)."</strong>.");
      writeLog("Document ".$trackno." has been added by ".$_SESSION['fullname']."(".$_SESSION['uid'].").");
      header("Location: ./?q=".$trackno);
      break;
      ?>
      <?php
    
    case 'receive':
      if(isset($_POST['trackingnumber']))
      {
        $stmt=$conn->prepare("INSERT INTO documentlog(trackingnumber,remarks,user) VALUES(?,?,?)");
        if($stmt === false) {
          trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
        }
        $userid=(isLoggedIn()?$_SESSION['uid']:0);
        $stmt->bind_param('isi',$_POST['trackingnumber'],$_POST['txtremarks'],$userid);
        $stmt->execute();

        setNotification("Document ".$_POST['trackingnumber']."'s status has been updated.");
        writeLog("Document ".$_POST['trackingnumber']." was received at ".$_SESSION['department']." (".$_SESSION['section'].").");
        header("Location: ./?q=".$_POST['trackingnumber']);
      }
      else
      {
        header("Location: ./");
      }
      break;
    
    default :
      displayHTMLPageHeader(); 
      ?>

      <?php
      displayHTMLPageFooter();
    endswitch;
    dbClose();
elseif((isset($_GET['page'])) && ($_GET['page']=='login')):
  global $conn;
  dbConnect();
  $stmt=$conn->prepare("SELECT uid, fullname, department, section FROM user WHERE uid=? AND password=?");
  if($stmt === false) {
    trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
  }
  $stmt->bind_param('is',$_POST['uid'],$_POST['password']);
  $stmt->execute();
  $stmt->store_result();
  if($stmt->num_rows==1)
  {
    $stmt->bind_result($_SESSION['uid'],$_SESSION['fullname'],$_SESSION['department'],$_SESSION['section']);
    while($stmt->fetch()){}
    writeLog($_SESSION['fullname']."(".$_SESSION['uid'].") logged in to the system.");
  }
  else
  {
    setNotification("Wrong ID Number and/or password.",DT_NOTIF_ERROR);
  }
  $stmt->close();
  dbClose();
  header("Location:".urldecode($_POST['lasturl']));
else:
  displayHTMLPageHeader();
    ?>
    <div class="loginpage">
      <div class="ui-block-a">
    <?php
    
    ?>
      </div>
      <div class="ui-block-b">
        <div class="ui-body">
          <p>Track and trace the documents at the tip of your fingers.</p>
        </div>
      </div>
    </div> 
    <?php  
    displayHTMLPageFooter();
endif;
?>
