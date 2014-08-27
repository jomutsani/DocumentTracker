<?php
//Initialize script
require_once 'functions.php';
session_start();
global $systempage;
$systempage=(is_null(filter_input(INPUT_GET, "page"))?"dashboard":filter_input(INPUT_GET, "page"));

if(!is_null($systempage))
{
    switch($systempage)
    {
        case "login":
            global $conn;
            dbConnect();
            $stmt=$conn->prepare("SELECT uid, fullname, department, section, permission+0 FROM user WHERE uid=? AND password=?");
            if($stmt === false) {
                trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
            }
            $postusername=filter_input(INPUT_POST, "uid");
            $postpassword=md5(filter_input(INPUT_POST, "password"));
            $stmt->bind_param('is',$postusername,$postpassword);
            $stmt->execute();
            $stmt->store_result();
            if($stmt->num_rows==1)
            {
                $stmt->bind_result($_SESSION['uid'],$_SESSION['fullname'],$_SESSION['department'],$_SESSION['section'], $_SESSION['permission']);
                while($stmt->fetch()){}
                $_SESSION['permlist']=  parsePermission($_SESSION['permission']);
                writeLog($_SESSION["fullname"]."(".$_SESSION["uid"].") logged in to the system.");
            }
            else
            {
                setNotification("Wrong ID Number and/or password.",DT_NOTIF_ERROR);
            }
            $stmt->close();
            dbClose();
            header("Location:".urldecode(filter_input(INPUT_POST, "lasturl")));
            break;
        case "logout":
            session_destroy();
            setNotification("Successfully logged out.");
            header("Location: ./");
            break;
        case "add":
            if(isLoggedIn() && checkPermission(DT_PERM_ADDDOC))
            {
                displayHTMLPageHeader();?>
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
                <?php displayHTMLPageFooter();
            }else{header("Location: ./");}
            break;
        case "adddoc":
            if(isLoggedIn() && checkPermission(DT_PERM_ADDDOC))
            {
                global $conn;
                dbConnect();
                $stmt=$conn->prepare("INSERT INTO document(documentnumber,remarks,author) VALUES(?,?,?)");
                if($stmt === false) {
                    trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                }
                $userid=(isLoggedIn()?$_SESSION["uid"]:0);
                $postdocnumber=filter_input(INPUT_POST, "documentnumber");
                $postremarks=filter_input(INPUT_POST, "remarks");
                $stmt->bind_param('ssi',$postdocnumber,$postremarks,$userid);
                $stmt->execute();
                $trackno = $stmt->insert_id;
                $stmt->close();

                $stmt2=$conn->prepare("INSERT INTO documentlog(trackingnumber,remarks,user) VALUES(?,?,?)");
                if($stmt2 === false) {
                    trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                }
                $msgremarks="Document received at ".$_SESSION["department"]." (".$_SESSION["section"]."). Document Remarks: ".filter_input(INPUT_POST, "remarks");
                $stmt2->bind_param('isi',$trackno,$msgremarks,$userid);
                $stmt2->execute();
                $stmt->close();

                setNotification("Document was successfully added. Tracking number is <strong>".str_pad($trackno,8,"0",STR_PAD_LEFT)."</strong>.");
                writeLog("Document ".$trackno." has been added by ".$_SESSION["fullname"]."(".$_SESSION["uid"].").");
                dbClose();
                header("Location: ./?q=".$trackno);
            }else{header("Location: ./");}
            break;
        case "receive":
            if(isLoggedIn() && checkPermission(DT_PERM_RECEIVEDOC))
            {
                if(!is_null(filter_input(INPUT_POST, "trackingnumber")))
                {
                    global $conn;
                    dbConnect();
                    $stmt=$conn->prepare("INSERT INTO documentlog(trackingnumber,remarks,user) VALUES(?,?,?)");
                    if($stmt === false) {
                        trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                    }
                    $userid=(isLoggedIn()?$_SESSION["uid"]:0);
                    $posttrackingnumber=  filter_input(INPUT_POST, "trackingnumber");
                    $posttxtremarks=  filter_input(INPUT_POST, "txtremarks");
                    $stmt->bind_param('isi',$posttrackingnumber,$posttxtremarks,$userid);
                    $stmt->execute();

                    setNotification("Document ".filter_input(INPUT_POST, "trackingnumber")."'s status has been updated.");
                    writeLog("Document ".filter_input(INPUT_POST, "trackingnumber")." was received at ".$_SESSION["department"]." (".$_SESSION["section"].").");
                    dbClose();
                    header("Location: ./?q=".filter_input(INPUT_POST, "trackingnumber"));
                }
                else
                {
                    header("Location: ./");
                }
            }else{header("Location: ./");}
            break;
        case "regform":
            if(isLoggedIn() && checkPermission(DT_PERM_USERMGMNT))
            {
                $uid = filter_input(INPUT_GET,"id");
                if($uid){
                    global $conn;
                    dbConnect();
                    
                    $stmt = $conn->prepare("SELECT fullname,department,section, permission FROM user WHERE uid=?");
                    $stmt->bind_param("i",$uid);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($fullname,$department,$section,$permission);
                    if($stmt->num_rows<1){
                        setNotification("No such user exists",DT_NOTIF_ERROR);
                        header("Location: ./users");
                    }
                    while($stmt->fetch()){}
                    $permission = parsePermission($permission);
                    $stmt->free_result();
                    $stmt->close();
                    
                    dbClose();
                }
                displayHTMLPageHeader();?>
                <header><h1><?php echo $uid?"Edit User":"User Registration"; ?></h1></header>
                <article>
                <form action="<?php echo !$uid?"./reguser":"./edituser"; ?>" method="post" data-ajax="false">
                    <?php if($uid): ?>
                        <h3>Employee Number: <?php echo $uid; ?></h3>
                        <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                    <?php else: ?>
                        <label for="uid">Employee ID Number</label>
                        <input type="number" name="uid" id="uid" required="true"/>
                    <?php endif; ?>

                    <label for="fullname">Full Name</label>
                    <input type="text" name="fullname" id="fullname" required="true" <?php echo $uid?'value="'.$fullname.'"':''; ?> />
                    
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" onchange="$('#password2').prop('pattern',this.value);" <?php echo $uid?'placeholder="Leave it blank if your don\'t want to change password."':'required="true" '; ?> />
                    
                    <label for="password2">Confirm Password</label>
                    <input type="password" name="password2" id="password2"  <?php echo $uid?'placeholder="Leave it blank if your don\'t want to change password."':'required="true" '; ?> />
                    
                    <label for="department">Department</label>
                    <input type="text" name="department" id="department" required="true" <?php echo $uid?'value="'.$department.'"':''; ?> />
                    
                    <label for="section">Section</label>
                    <input type="text" name="section" id="section" required="true" <?php echo $uid?'value="'.$section.'"':''; ?> />
                    
                    <fieldset data-role="controlgroup">
                        <legend>Permissions</legend>
                        <input type="checkbox" name="p[]" id="checkbox01" value="1" <?php echo $uid?(checkPermission(DT_PERM_ADDDOC,$permission)?'checked="checked"':''):'checked="checked"'; ?>/>
                        <label for="checkbox01">Add Document</label>
                        <input type="checkbox" name="p[]" id="checkbox02" value="2" <?php echo $uid?(checkPermission(DT_PERM_EDITDOC,$permission)?'checked="checked"':''):''; ?>/>
                        <label for="checkbox02">Edit Document</label>
                        <input type="checkbox" name="p[]" id="checkbox03" value="4" <?php echo $uid?(checkPermission(DT_PERM_RECEIVEDOC,$permission)?'checked="checked"':''):'checked="checked"'; ?>/>
                        <label for="checkbox03">Receive Document</label>
                        <input type="checkbox" name="p[]" id="checkbox04" value="8" <?php echo $uid?(checkPermission(DT_PERM_EDITDOCTRACK,$permission)?'checked="checked"':''):''; ?>/>
                        <label for="checkbox04">Edit Document Track</label>
                        <input type="checkbox" name="p[]" id="checkbox05" value="16" <?php echo $uid?(checkPermission(DT_PERM_USERMGMNT,$permission)?'checked="checked"':''):''; ?>/>
                        <label for="checkbox05">User Management</label>
                        <input type="checkbox" name="p[]" id="checkbox06" value="32" <?php echo $uid?(checkPermission(DT_PERM_AUDITLOG,$permission)?'checked="checked"':''):''; ?>/>
                        <label for="checkbox06">Audit Log</label>
                    </fieldset>

                    <input type="submit" value="<?php echo $uid?"Update":"Register"; ?>" data-icon="edit" data-ajax="false"/>
                </form>
                </article>
                <?php displayHTMLPageFooter();
            }else{header("Location: ./");}
            break;
        case "reguser":
            if(isLoggedIn() && checkPermission(DT_PERM_USERMGMNT))
            {
                if(!is_null(filter_input(INPUT_POST, "uid")))
                {
                    global $conn;
                    dbConnect();
                    $stmt=$conn->prepare("INSERT INTO user(uid,password,fullname,department,section,permission) VALUES(?,?,?,?,?,?)");
                    if($stmt === false) {
                        trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                        break;
                    }
                    $userid=(isLoggedIn()?$_SESSION["uid"]:0);
                    $uid = filter_input(INPUT_POST, "uid");
                    $password=md5(filter_input(INPUT_POST, "password"));
                    $fullname=filter_input(INPUT_POST, "fullname");
                    $department=filter_input(INPUT_POST, "department");
                    $section=filter_input(INPUT_POST, "section");
                    $pcount=filter_input(INPUT_POST, "p");
                    $permission=0;
                    while(list($key,$val)=@each($pcount)) {
                        $permission += intval($val);
                    }
                    
                    $stmt->bind_param('issssi',$uid,$password,$fullname,$department,$section,$permission);
                    $stmt->execute();

                    setNotification("User ".$fullname."(".$uid.") has been registered.");
                    writeLog("User ".$fullname."(".$uid.") has been registered.");
                    dbClose();
                    header("Location: ./");
                }
                else
                {
                    header("Location: ./");
                }
            }else{header("Location: ./");}
            break;
        case "edituser":
            if(isLoggedIn() && checkPermission(DT_PERM_USERMGMNT))
            {
                global $conn;
                dbConnect();
                $userid=(isLoggedIn()?$_SESSION["uid"]:0);
                $uid = filter_input(INPUT_POST, "uid");
                $password=md5(filter_input(INPUT_POST, "password"));
                $fullname=filter_input(INPUT_POST, "fullname");
                $department=filter_input(INPUT_POST, "department");
                $section=filter_input(INPUT_POST, "section");
                $pcount=filter_input_array(INPUT_POST)["p"];
                $permission=0;
                while(list($key,$val)=@each($pcount)) {
                    $permission += intval($val);
                }

                if(filter_input(INPUT_POST, "password")=="")
                {
                    $stmt=$conn->prepare("UPDATE user SET fullname=?, department=?, section=?, permission=? WHERE uid=?");
                    if($stmt === false) {
                        trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                        break;
                    }
                    $stmt->bind_param('sssii',$fullname,$department,$section,$permission,$uid);
                }else{
                    $stmt=$conn->prepare("UPDATE user SET fullname=?, password=?, department=?, section=?, permission=? WHERE uid=?");
                    if($stmt === false) {
                        trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                        break;
                    }
                    $stmt->bind_param('ssssii',$fullname,$password,$department,$section,$permission,$uid);
                }
                $stmt->execute();
                setNotification("User ".$fullname."(".$uid.") has been updated.");
                writeLog("User ".$fullname."(".$uid.") has been updated to Name=".$fullname.", Dept=".$department.", Section=".$section.", Perm=".$permission.(filter_input(INPUT_POST, "password")==""?"":", Password=".$password));
                dbClose();
                header("Location: ./regform?id=".$uid);
            }else{header("Location: ./");}
            break;
        case "users":
            if(isLoggedIn() && checkPermission(DT_PERM_USERMGMNT))
            {
                ?>
                        
                    <?php 
                displayHTMLPageHeader();?>
                <header><h1>Users</h1></header>
                <article>
                    <a href="./regform" data-role="button" data-icon="plus" data-inline="true">Add User</a>
                    <table class="ui-body ui-responsive table-stripe" data-role="table" data-mode="reflow">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php global $conn;
                            dbConnect();
                            $stmt=$conn->prepare("SELECT uid, fullname, department, section FROM user");
                            if($stmt === false) {
                                trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                            }
                            $stmt->execute();
                            $stmt->store_result();
                            if($stmt->num_rows>0)
                            {
                                $stmt->bind_result($uid,$fullname,$department,$section);
                                while($stmt->fetch()): ?>
                                <tr>
                                    <td><?php echo $fullname; ?></td>
                                    <td><a href="./regform?id=<?php echo $uid; ?>" data-role="button" data-icon="edit">Edit</a></td>
                                </tr>
                            <?php endwhile;
                                $_SESSION['permlist']=  parsePermission($_SESSION['permission']);
                                writeLog($_SESSION["fullname"]."(".$_SESSION["uid"].") logged in to the system.");
                            }
                            else
                            {
                                setNotification("Wrong ID Number and/or password.",DT_NOTIF_ERROR);
                            }
                            $stmt->close(); ?>
                        </tbody>
                    </table>
                </article>
                <?php displayHTMLPageFooter();
            }else{header("Location: ./");}
            break;
        case "auditlogss":
            if(isLoggedIn() && checkPermission(DT_PERM_AUDITLOG))
            {
                $table = 'auditlog a INNER JOIN user b ON b.uid=a.user';// LEFT JOIN charges b ON a.id=b.homeowner LEFT JOIN ledgeritem d ON d.chargeid=b.id LEFT JOIN ledger e ON e.id=d.ledgerid';
                $primaryKey = 'id';
                $columns = array(
                    //array('db'=>'id','dt'=>0),
                    array('db'=>'a.ts','dt'=>0,'alias'=>'ts'),
                    array('db'=>'CONCAT(b.fullname," (",a.user,")")','dt'=>1,'alias'=>'fullname'),
                    array('db'=>'a.page','dt'=>2,'alias'=>'page'),
                    array('db'=>'a.msg','dt'=>3,'alias'=>'msg')
                );
                $addwhere="";
                $group="GROUP BY a.id";
                $counttable="auditlog";
                $countwhere="";
                $sql_details = array('user'=>DT_DB_USER,'pass'=>DT_DB_PASSWORD,'db'=>DT_DB_NAME,'host'=>DT_DB_SERVER);
                require('ssp.class.php');
                echo json_encode(SSP::customQuery(filter_input_array(INPUT_GET), $sql_details, $table, $primaryKey, $columns, $addwhere, $group, $counttable,$countwhere));
            }else{header("Location: ./");}
            break;
        case "auditlog":
            if(isLoggedIn() && checkPermission(DT_PERM_AUDITLOG))
            {
                global $conn;
                dbConnect();

//                $stmt = $conn->prepare("SELECT a.ts,a.user,b.fullname,a.page,a.msg FROM auditlog a INNER JOIN user b ON b.uid=a.user");
//                $stmt->execute();
//                $stmt->store_result();
//                $stmt->bind_result($ts,$uid,$fullname,$logpage,$msg); 
//                dbClose();
                displayHTMLPageHeader();?>
                <header><h1>Audit Log</h1></header>
                <article>
<!--                    <div class="ui-body ui-body-a ui-corner-all ui-shadow" style="margin-bottom: 10px;">
                        <form method="get">
                            <fieldset data-role="controlgroup" data-type="horizontal">
                                <legend>Filter</legend>
                                <label for="datestart">Start Date</label>
                                <input type="date" name="datestart" id="datestart" placeholder="Start Date" />
                                <label for="dateend">End Date</label>
                                <input type="datetime" name="dateend" id="dateend" placeholder="End Date" />
                                <label>User</label>
                                <select></select>
                            </fieldset>
                        </form>
                    </div>-->
                    
                    <div class="ui-body ui-body-a ui-corner-all ui-content ui-shadow">
                        <table data-type="table" data-mode="reflow" class="ui-responsive table-stripe" style="width:100%;" id="tbllog">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Page</th>
                                    <th>Message</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </article>
                <script type="text/javascript">
                    $(document).ready(function() {
                        ul = $('#tbllog').dataTable({
                        "processing": true,
                        "serverSide": true,
                        "ajax": "./auditlogss"});
                    
                        ulapi = ul.api();
                    
                        $("#tbllog").on( "init.dt", function() {
                            $("#tbllog_wrapper").enhanceWithin();
                            $(".dataTables_wrapper div.ui-select>div.ui-btn").addClass("ui-btn-a");
                            $("#tbllog_filter input").on("change",function(){
                                ulapi.search($(this).val()).draw();
                            });
                        });
                    });
                    
                </script>
                <?php displayHTMLPageFooter();
            }else{header("Location: ./");}
            break;
        default :
            displayHTMLPageHeader();
            displayHTMLPageFooter();
    }
}
