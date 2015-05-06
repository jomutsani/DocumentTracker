<?php
//Define Conn Properties
$conn;
$systempage;
define('DT_NOTIF_NORMAL', 0);
define('DT_NOTIF_WARNING', 1);
define('DT_NOTIF_ERROR', 2);
define('DT_DB_SERVER', 'localhost');
define('DT_DB_USER', "root");
define('DT_DB_PASSWORD', "P@ssw00rd");
define('DT_DB_NAME', "documenttracker");
define('DT_LOG_NAME',"DocumentTracker");
define('DT_PAGE_TITLE',"Document Tracker");
define('DT_PERMISSION_COUNT', 10);
define('DT_PERM_ADDDOC',0);
define('DT_PERM_EDITDOC',1);
define('DT_PERM_RECEIVEDOC',2);
define('DT_PERM_EDITDOCTRACK',3);
define('DT_PERM_USERMGMNT',4);
define('DT_PERM_AUDITLOG',5);
define('DT_PERM_REPORT',6);
define('DT_PERM_ADVSEARCH',7);
define('DT_PERM_HIDDENRECEIVE',8);
define('DT_PERM_REOPENDOC',9);
$utc=time();

function displayUserInfo()
{?>
    <section class="" data-role="panel" id="userpanel" data-position="right" data-position-fixed="true" data-display="overlay"><?php
    if(isLoggedIn())
    {?>
        <header><h1><?php echo $_SESSION['fullname']; ?></h1></header>
        <article>
          <table id="tbluserinfo" class="ui-body ui-responsive" data-role="table" data-mode="reflow">
            <thead>
              <tr>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <tbody>
            <tr>
              <td>ID Number</td>
              <td><?php echo $_SESSION['uid']; ?></td>
            </tr>
            <tr>
                <td>Full Name</td>
                <td><?php echo $_SESSION['fullname']; ?></td>
              </tr>
            <tr>
              <td>Department</td>
              <td><?php echo $_SESSION['department']; ?></td>
            </tr>
            <tr>
              <td>Section</td>
              <td><?php echo $_SESSION['section']; ?></td>
            </tr>
            </tbody>
          </table>
          <a href="./logout" data-role="button" data-icon="power" data-iconpos="left" data-ajax="false">Logout</a>
        </article><?php
    }
    else
    {?>
        <header><h1>Login</h1></header>
        <article>
          <form action="./login" method="post" data-ajax="false">
              <label for="uid">ID Number</label>
              <input type="text" name="uid" id="uid"/>

              <label for="password">Password</label>
              <input type="password" name="password" id="password"/>

              <input type="hidden" name="lasturl" value="<?php echo urlencode(curPageURL()); ?>"/>
              <input type="submit" value="Login" data-icon="forward"/>

          </form>
        </article><?php
    }?>
    </section><?php
}

function displayHTMLPageHeader($pagetitle=DT_PAGE_TITLE)
{?>
    <!DOCTYPE html>
    <html>
      <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $pagetitle; ?></title>
        <link rel="stylesheet" href="./css/jquery.mobile.structure-1.4.2.min.css" />
        <link rel="stylesheet" href="./css/jquery.mobile.theme-1.4.2.min.css" />
        <link rel="stylesheet" href="./css/jquery.mobile-1.4.2.min.css" />
        <link rel="stylesheet" href="./css/jquery.mobile.external-png-1.4.2.min.css" />
        <link rel="stylesheet" href="./css/jquery.mobile.icons-1.4.2.min.css" />
        <link rel="stylesheet" href="./css/jquery.mobile.inline-png-1.4.2.min.css" />
        <link rel="stylesheet" href="./css/jquery.mobile.inline-svg-1.4.2.min.css" />
        
        <link rel="stylesheet" href="./plugin/DataTables-1.10.0/media/css/jquery.dataTables_themeroller.min.css" />
        <link rel="stylesheet" href="./plugin/DataTables-1.10.0/integration/bootstrap/bin/bootstrap.css" />
        <link rel="stylesheet" href="./plugin/DataTables-1.10.0/integration/bootstrap/bin/dataTables.bootstrap.css" />
        <link rel="stylesheet" href="./plugin/DataTables-1.10.0/extensions/TableTools/css/dataTables.tableTools.min.css" />
        
        <link rel="stylesheet" href="./css/default.css" />
        <script src="./js/jquery-2.1.1.min.js"></script>
        <script src="./js/jquery.mobile-1.4.2.min.js"></script>
        
        <script src="./plugin/DataTables-1.10.0/media/js/jquery.dataTables.js"></script>
        <script src="./plugin/DataTables-1.10.0/integration/bootstrap/bin/dataTables.bootstrap.js"></script>
        <script src="./plugin/DataTables-1.10.0/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
        
        <script src="./plugin/barcode/jquery-barcode.min.js"></script>
        
        <script src="./js/default.js"></script>
      </head>
      <body>
        <div data-role="page">
        <header data-role="header">
          <h1>Document Tracker</h1>
          <a href="./" data-icon="home" data-iconpos="notext" class="ui-btn-left">Home</a>
          <a href="#userpanel" data-icon="user" data-iconpos="notext" class="ui-btn-right">Account</a>
        <?php
        if(isLoggedIn() && (checkPermission(DT_PERM_ADDDOC) || checkPermission(DT_PERM_USERMGMNT) || checkPermission(DT_PERM_AUDITLOG) || checkPermission(DT_PERM_REPORT))):
        ?>
          <div data-role="navbar">
              <ul>
                  <?php if(checkPermission(DT_PERM_ADDDOC)): ?><li><a href="./add" data-icon="plus">Add Document</a></li><?php endif;?>
                  <?php if(checkPermission(DT_PERM_USERMGMNT)): ?><li><a href="./users" data-icon="edit">User Management</a></li><?php endif;?>
                  <?php if(checkPermission(DT_PERM_AUDITLOG)): ?><li><a href="./auditlog" data-icon="eye">Audit Log</a></li><?php endif;?>
                  <?php if(checkPermission(DT_PERM_REPORT)): ?><li><a href="./reports" data-icon="bullets">Reports</a></li><?php endif;?>
                  <?php if(checkPermission(DT_PERM_ADVSEARCH)): ?><li><a href="./search" data-icon="search">Advanced Search</a></li><?php endif;?>
              </ul>
          </div>
        <?php
        endif;
        ?>
        </header>
        <div role="main" class="ui-content">
        <?php
        $utc=time();
        displayNotification();
}

function displayHTMLPageFooter(){
    ?>
        </div>
        <footer data-role="footer" data-position="">
          <strong>Quezon Document Tracker</strong>&COPY;2014 Developed by The Aitenshi Project
        </footer>
        <?php displayUserInfo(); ?>
        </div>
      </body>
    </html>      
    <?php
}

function displayPlainHTMLPageHeader($pagetitle=DT_PAGE_TITLE)
{?>
    <!DOCTYPE html>
    <html>
      <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $pagetitle; ?></title>
<!--        <link rel="stylesheet" href="./css/jquery.mobile.structure-1.4.2.min.css" />
        <link rel="stylesheet" href="./css/jquery.mobile.theme-1.4.2.min.css" />
        <link rel="stylesheet" href="./css/jquery.mobile-1.4.2.min.css" />
        <link rel="stylesheet" href="./css/jquery.mobile.external-png-1.4.2.min.css" />
        <link rel="stylesheet" href="./css/jquery.mobile.icons-1.4.2.min.css" />
        <link rel="stylesheet" href="./css/jquery.mobile.inline-png-1.4.2.min.css" />
        <link rel="stylesheet" href="./css/jquery.mobile.inline-svg-1.4.2.min.css" />-->
        <link rel="stylesheet" href="./css/reportstyle.css" />
        
        <link rel="stylesheet" href="./plugin/DataTables-1.10.0/media/css/jquery.dataTables_themeroller.min.css" />
        <link rel="stylesheet" href="./plugin/DataTables-1.10.0/integration/bootstrap/bin/bootstrap.css" />
        <link rel="stylesheet" href="./plugin/DataTables-1.10.0/integration/bootstrap/bin/dataTables.bootstrap.css" />
        <link rel="stylesheet" href="./plugin/DataTables-1.10.0/extensions/TableTools/css/dataTables.tableTools.min.css" />
        
        <link rel="stylesheet" href="./css/default.css" />
        <script src="./js/jquery-2.1.1.min.js"></script>
<!--        <script src="./js/jquery.mobile-1.4.2.min.js"></script>-->
        
        <script src="./plugin/DataTables-1.10.0/media/js/jquery.dataTables.js"></script>
        <script src="./plugin/DataTables-1.10.0/integration/bootstrap/bin/dataTables.bootstrap.js"></script>
        <script src="./plugin/DataTables-1.10.0/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
        
        <script src="./plugin/barcode/jquery-barcode.min.js"></script>
        
        <script src="./js/default.js"></script>
      </head>
      <body>
<!--        <div data-role="page">
        <header data-role="header">
          <h1>Document Tracker</h1>
          <a href="./" data-icon="home" data-iconpos="notext" class="ui-btn-left">Home</a>
          <a href="#userpanel" data-icon="user" data-iconpos="notext" class="ui-btn-right">Account</a>
        <?php
        if(isLoggedIn() && $_SESSION["permission"]>0):
        ?>
          <div data-role="navbar">
              <ul>
                  <?php if(checkPermission(DT_PERM_ADDDOC)): ?><li><a href="./add" data-icon="plus">Add Document</a></li><?php endif;?>
                  <?php if(checkPermission(DT_PERM_USERMGMNT)): ?><li><a href="./users" data-icon="edit">User Management</a></li><?php endif;?>
                  <?php if(checkPermission(DT_PERM_AUDITLOG)): ?><li><a href="./auditlog" data-icon="eye">Audit Log</a></li><?php endif;?>
              </ul>
          </div>
        <?php
        endif;
        ?>
        </header>-->
        <div role="main" class="ui-content">
<?php }

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
        <ul data-role="listview" data-inset="true" id="notif" class="notification">
        <li data-iconpos="left" data-icon="<?php switch($_COOKIE['notiftype']){case DT_NOTIF_NORMAL:echo "info"; break; case DT_NOTIF_WARNING:echo "alert"; break; case DT_NOTIF_ERROR: echo "delete"; break;} ?>" class="notif<?php echo $_COOKIE['notiftype']; ?>"><a href="#" class=""><?php echo $_COOKIE['notifmsg']; ?></a></li>
        </ul>
        <?php
        setcookie("notifmsg",null,time()-3600);
        setcookie("notiftype",null,time()-3600);
    }
}

function writeLog($msg, $type="Info")
{
  global $conn;
  $stmt=$conn->prepare("INSERT INTO auditlog(type,user,page,msg) VALUES(?,?,?,?)");
  if($stmt === false) {
    trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
  }
  $userid=(isLoggedIn()?$_SESSION['uid']:0);
  $page=(isset($_GET['page'])?$_GET['page']:"dashboard");
  $stmt->bind_param('siss',$type,$userid,$page,$msg);
  $stmt->execute();
}

function displaySearchResult()
{
    global $utc;
  if((isset($_GET['q'])) && ($_GET['q']!='')):
    if(isset($_GET['q']))
    {
      global $conn;
      dbConnect();
      $stmt=$conn->prepare("SELECT a.id, a.barcodenumber, a.documentcategory, c.description, a.documentnumber, a.remarks, a.datecreated, a.author, b.uid, b.fullname, b.department, b.section, a.end FROM document a INNER JOIN user b ON a.author=b.uid INNER JOIN documentcategory c ON a.documentcategory=c.id WHERE barcodenumber=?");
      if($stmt === false) {
        trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
      }
      $searchquery=  filter_input(INPUT_GET, "q", FILTER_SANITIZE_STRING);
      $stmt->bind_param('s',$searchquery);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($r_trackingnumber, $r_barcodenumber, $r_documentcategory, $r_doccategory, $r_documentnumber, $r_remarks, $r_datecreated, $r_author, $r_uid, $r_fullname, $r_department, $r_section, $r_end);

      if($stmt->num_rows <= 0)
      {
        ?>
          <h1>Nothing found</h1>
          <p>Please verify that you have the correct tracking number and try again.</p>
        <?php
      }
      else
      {
        while($stmt->fetch()){
            $bcodeid="bcode".md5(time());
          ?>
          <div data-role="collapsible" data-collapsed="false">
              <h4>Tracking #: <?php echo $r_barcodenumber; ?><?php echo ($r_end==1?" | [END]":""); ?></h4>
              <table class="documenttableinfo">
                <thead>
                  <tr>
                    <th></th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Document Type</td>
                    <td><?php echo $r_doccategory; ?></td>
                  </tr>
                  <tr>
                    <td>Document Number</td>
                    <td><?php echo $r_documentnumber; ?></td>
                  </tr>
                  <tr>
                    <td>Remarks</td>
                    <td><?php echo $r_remarks; ?></td>
                  </tr>
                  <tr>
                    <td>Date received</td>
                    <td><?php echo $r_datecreated; ?></td>
                  </tr>
                  <tr>
                    <td>Department</td>
                    <td><?php echo $r_department." (".$r_section.")"; ?></td>
                  </tr>
                  <tr>
                      <td>Barcode</td>
                      <td><div id="<?php echo $bcodeid; ?>" class="barcodeTarget"><?php echo $r_barcodenumber; ?></div></td>
                  </tr>
                </tbody>
              </table>
              <?php //echo debug_bind_param("SELECT a.trackingnumber, a.barcodenumber, a.documentcategory, c.description, a.documentnumber, a.remarks, a.datecreated, a.author, b.uid, b.fullname, b.department, b.section FROM document a INNER JOIN user b ON a.author=b.uid INNER JOIN documentcategory c ON a.documentcategory=c.id WHERE barcodenumber=?",'i',$_GET['q']);?>
              <?php
              if(isLoggedIn() && checkPermission(DT_PERM_RECEIVEDOC) && $r_end==0):
              ?>
                <a href="#receiveDialog<?php echo $r_trackingnumber.'_'.$utc; ?>" data-role="button" data-inline="true" data-icon="arrow-d" data-rel="popup" data-position-to="window" data-transition="pop">Receive Document</a>
                
                <div data-role="popup" id="receiveDialog<?php echo $r_trackingnumber.'_'.$utc; ?>" data-dismissible="false" data-overlay-theme="b">
                  <header data-role="header">
                    <h1>Receive Document</h1>
                    <a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a>
                  </header>
                  <div role="main" class="ui-content">
                    <h3>Tracking #: <?php echo $r_barcodenumber; ?></h3>
                    <form action="./receive" method="post" data-ajax="false">
                      <label for="txtremarks" class="ui-hidden-accessible">Remarks</label>
                      <textarea name="txtremarks" id="txtremarks" placeholder="Remarks"></textarea>
                      <label for="finalrelease">
                          <input type="checkbox" name="finalrelease" id="chkfinal" value="1"/>
                        Final Release
                      </label>
                      <?php if(checkPermission(DT_PERM_HIDDENRECEIVE)): ?>
                        <label for="hiddenreceive">
                            <input type="checkbox" name="hiddenreceive" id="chkhiddenreceive" value="1"/>
                          Hidden
                        </label>
                      <?php else: ?>
                        <input type="hidden" name="hiddenreceive" value="0"/>
                      <?php endif; ?>
                      <input type="hidden" name="trackingnumber" value="<?php printf("%08d",$r_trackingnumber); ?>"/>
                      <input type="hidden" name="barcodenumber" value="<?php echo $r_barcodenumber; ?>"/>
                      <input type="submit" value="Receive" data-icon="arrow-d"/>
                    </form>
                  </div>
                </div>
              <?php
              endif;
              
              if(isLoggedIn() && checkPermission(DT_PERM_REOPENDOC) && $r_end==1): ?>
                <a href="#reopenDialog<?php echo $utc; ?>" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-icon-action ui-btn-icon-left">Reopen Document</a>
                <div data-role="popup" id="reopenDialog<?php echo $utc; ?>" data-dismissible="false" style="max-width:400px;" data-overlay-theme="b">
                    <div data-role="header"><h1>Reopen Document?</h1></div>
                    <div role="main" class="ui-content">
                        <h3 class="ui-title">Are you sure you want to reopen this document?</h3>
                        <form action="./reopen" method="post" data-ajax="false">
                            <input type="hidden" name="docid" value="<?php echo $r_trackingnumber; ?>"/>
                            <input type="hidden" name="barcodenumber" value="<?php echo $r_barcodenumber; ?>"/>
                            <input type="submit" value="Yes"/>
                            <a href="#" data-role="button" data-rel="back" data-transition="pop">No</a>
                        </form>
                    </div>
                </div>
              <?php endif;
              ?>
            <?php
                if(isLoggedIn() && checkPermission(DT_PERM_EDITDOC)):
            ?>
                <a href="./edit?id=<?php echo $r_trackingnumber; ?>" data-role="button" data-icon="edit" data-inline="true">Edit Document</a>
            <?php
                endif;
            ?>
          </div>
        <?php
          global $conn;
          dbConnect();
          $stmt2=$conn->prepare("SELECT a.logid, a.docid,a.ts,a.remarks,a.user, b.uid, b.fullname, b.department, b.section, a.visible FROM documentlog a INNER JOIN user b ON a.user=b.uid WHERE docid=?".(checkPermission(DT_PERM_HIDDENRECEIVE)?"":" AND a.visible=1")." ORDER BY a.ts DESC");
          if($stmt2 === false) {
            trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
          }
          $stmt2->bind_param('i',$r_trackingnumber);
          $stmt2->execute();
          $stmt2->store_result();
          $stmt2->bind_result($r2_logid,$r2_trackingnumber,$r2_ts,$r2_remarks,$r2_user,$r2_uid,$r2_fullname,$r2_department,$r2_section,$r2_visible);
        ?>
        <h1>Document Log</h1>
        <table data-role='table' class='ui-responsive table-stripe ui-body-a ui-shadow'>
          <thead>
            <tr>
              <th>Date</th>
              <th>Remarks</th>
              <th>Staff</th>
              <th>Department</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?php
            $editonce=false;
            while($stmt2->fetch())
            {
              ?>
              <tr <?php echo (!$r2_visible?"class='loghidden'":"");?>>
                  <td><?php echo $r2_ts;?></td>
                  <td><?php echo $r2_remarks;?></td>
                  <td><?php echo $r2_fullname." (".$r2_uid.")";?></td>
                  <td><?php echo $r2_department." (".$r2_section.")";?></td>
                  <td>
                      <?php if(!$editonce): ?>
                      <?php if(isLoggedIn() && checkPermission(DT_PERM_EDITDOCTRACK) && $r2_uid==$_SESSION['uid']): ?>
                      <a href="#editReceiveDialog<?php echo $r2_logid.'_'.$utc; ?>" data-role="button" data-inline="true" data-iconpos="notext" data-icon="edit" data-mini="true" data-rel="popup" data-position-to="window" data-transition="pop">Receive Document</a>
                        <div data-role="popup" id="editReceiveDialog<?php echo $r2_logid.'_'.$utc; ?>" data-dismissible="false" data-overlay-theme="b">
                            <header data-role="header">
                              <h1>Edit Remarks</h1>
                              <a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a>
                            </header>
                            <div role="main" class="ui-content">                                
                                <form action="./editreceive" method="post" data-ajax="false">
                                    <input type="hidden" name="trackingnumber" value="<?php echo $r_trackingnumber; ?>"/>
                                    <input type="hidden" name="logid" value="<?php echo $r2_logid; ?>"/>
                                    <label for="txtremarks" class="ui-hidden-accessible">Remarks</label>
                                    <textarea name="txtremarks" id="txtremarks" placeholder="Remarks"><?php echo $r2_remarks; ?></textarea>
                                    <?php if(checkPermission(DT_PERM_REOPENDOC)): ?>
                                    <label for="finalrelease">
                                        <input type="checkbox" name="finalrelease" id="chkfinal" value="1" <?php echo ($r_end==1?"checked='checked'":""); ?>/>
                                      Final Release
                                    </label>
                                    <?php else: ?>
                                        <input type="hidden" name="finalrelease" value="<?php echo $r_end; ?>"/>
                                    <?php endif; ?>
                                    <?php if(checkPermission(DT_PERM_HIDDENRECEIVE)): ?>
                                      <label for="hiddenreceive">
                                          <input type="checkbox" name="hiddenreceive" id="chkhiddenreceive" value="1" <?php echo ($r2_visible==0?"checked='checked'":""); ?>/>
                                        Hidden
                                      </label>
                                    <?php else: ?>
                                      <input type="hidden" name="hiddenreceive" value="0"/>
                                    <?php endif; ?>
                                   
                                    <input type="hidden" name="barcodenumber" value="<?php echo $r_barcodenumber; ?>"/>
                                    <input type="submit" value="Modify" data-icon="arrow-d"/>
                                  </form>
                            </div>
                        </div>
                      <?php endif; ?>
                      <?php $editonce=true; endif; ?>
                  </td>
                </tr>
              <?php
            }
          ?>
          </tbody>
        </table>
        <script type="text/javascript">
            
            
            
        </script>
          <?php
          $stmt2->close();
        }
      }
      $stmt->close();
    }
  endif;
}

function isLoggedIn()
{
    return (isset($_SESSION['uid'])?true:false);
}

function parsePermission($p){
    return str_split(strrev(str_pad(decbin($p), DT_PERMISSION_COUNT, "0", STR_PAD_LEFT)));
}

function checkPermission($p,$a=NULL)
{
    if(is_null($a)){
        $a=$_SESSION['permlist'];
    }
    return (isset($a)?(($a[$p]=="1")?true:false):false);
}

function displayPrintHeader()
{
    ?>
        <header class="printheader">
            <img src="quezonseal.jpg" alt="Quezon Logo" class="headerlogo"/>
            <div class="orgname">Provincial Government of Quezon</div>
        </header>
    <?php
}

function debug_bind_param(){
    $numargs = func_num_args();
    $numVars = $numargs - 2;
    $arg2 = func_get_arg(1);
    $flagsAr = str_split($arg2);
    $showAr = array();
    for($i=0;$i<$numargs;$i++){
        switch($flagsAr[$i]){
        case 's' :  $showAr[] = "'".func_get_arg($i+2)."'";
        break;
        case 'i' :  $showAr[] = func_get_arg($i+2);
        break;  
        case 'd' :  $showAr[] = func_get_arg($i+2);
        break;  
        case 'b' :  $showAr[] = "'".func_get_arg($i+2)."'";
        break;  
        }
    }
    $query = func_get_arg(0);
    $querysAr = str_split($query);
    $lengthQuery = count($querysAr);
    $j = 0;
    $display = "";
    for($i=0;$i<$lengthQuery;$i++){
        if($querysAr[$i] === '?'){
            $display .= $showAr[$j];
            $j++;   
        }else{
            $display .= $querysAr[$i];
        }
    }
    if($j != $numVars){
        $display = "Mismatch on Variables to Placeholders (?)"; 
    }
    return $display;
}

function getNextPageStr()
{
    parse_str($_SERVER['QUERY_STRING'], $q_array);
    $q_array['p']++;
    unset($q_array['page']);
    return http_build_query($q_array);
}
function getPrevPageStr()
{
    parse_str($_SERVER['QUERY_STRING'], $q_array);
    $q_array['p']--;
    unset($q_array['page']);
    return http_build_query($q_array);
}
?>
