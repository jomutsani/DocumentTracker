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
            $stmt=$conn->prepare("SELECT uid, fullname, department, division, section, permission+0 FROM user WHERE uid=? AND password=?");
            if($stmt === false) {
                trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
            }
            $postusername=filter_input(INPUT_POST, "uid");
            $postpassword=md5(filter_input(INPUT_POST, "password"));
            $stmt->bind_param('is',$postusername,$postpassword);
            $stmt->execute();
            $stmt->store_result();
            var_dump($postpassword);
            var_dump($stmt->num_rows);
            if($stmt->num_rows==1)
            {
                $stmt->bind_result($_SESSION['uid'],$_SESSION['fullname'],$_SESSION['department'],$_SESSION['division'],$_SESSION['section'], $_SESSION['permission']);
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
                $category="";
                global $conn;
                dbConnect();

                $stmt2=$conn->prepare("SELECT id,description FROM documentcategory ORDER BY description ASC");
                if($stmt2 === false) {
                    trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                }
                $stmt2->execute();
                $stmt2->store_result();

                if($stmt2->num_rows>0){
                    $stmt2->bind_result($uid,$uname);
                    while($stmt2->fetch()):
                        $category .= '<option value="'.$uid.'">'.$uname.'</option>'; 
                    endwhile;
                }
                $stmt2->free_result();
                displayHTMLPageHeader();?>
                <header><h1>Add Document</h1></header>
                <article>
                <form action="./adddoc" method="post" data-ajax="false">
                    <label for="category-filter-menu">Document Category</label>
                    <select id="category-filter-menu" class="filterable-select" name="category-filter-menu" data-native-menu="false" required="true" data-inline="true" value="<?php echo (is_null($t=filter_input(INPUT_GET, 'category-filter-menu',FILTER_SANITIZE_NUMBER_INT))?"":$t); ?>" autofocus="true">
                        <?php echo $category; ?>
                    </select>
                    
                    <label for="documentnumber">Document Number</label>
                    <input type="text" name="documentnumber" id="documentnumber"/>

                    <label for="remarks">Remarks</label>
                    <input type="text" name="remarks" id="remarks"/>

                    <!--<ul data-role="listview" data-inset="true" class="notification">
                        <li data-iconpos="left" data-icon="info" class="notif0"><a class="">Bar</a></li>
                    </ul>-->
                   
<!--                    <div id="popupBarcodeHelp" data-arrow="true">
                        <p>This is a completely basic popup, no options set.</p>
                    </div>-->
                    <div class="ui-corner-all custom-corners">
                        <div class="ui-bar ui-bar-a">
                            <label for="barcodenumber" id="barcodenumberlabel"><strong>Barcode Number</strong></label>
                        </div>
                        <div class="ui-body ui-body-a">
                            
                            <div class="ui-content"><strong>Note for Barcode Reader Users:</strong> Please fill up all the necessary fields first before scanning the barcode. Scanning the barcode might automatically submit the form. </div>
                            <input type="text" name="barcodenumber" id="barcodenumber" title="" required="required" placeholder="Do not scan the barcode yet."/>
                        </div>
                    </div>
                    <input type="submit" value="Add" data-icon="plus" data-ajax="false"/>
                </form>
                </article>
                <script type="text/javascript">
                    $(document).ready(function(){
                        $("#barcodenumber").focusin(function(){
                            $(this).attr("placeholder","You may now scan the barcode.");    
                        });
                        
                        $("#barcodenumber").focusout(function(){
                            $(this).attr("placeholder","Do not scan the barcode yet.");    
                        });
                    });
                </script>
                <?php displayHTMLPageFooter();
                dbClose();
            }else{header("Location: ./");}
            break;
        case "edit":
            if(isLoggedIn() && checkPermission(DT_PERM_EDITDOC))
            {
                $tid = filter_input(INPUT_GET,"id");
                if($tid){
                    global $conn;
                    $category="";
                    dbConnect();
                    
                    $stmt = $conn->prepare("SELECT documentnumber,remarks,documentcategory,barcodenumber FROM document WHERE id=?");
                    $stmt->bind_param("i",$tid);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($documentnumber,$remarks,$documentcategory,$barcodenumber);
                    if($stmt->num_rows<1){
                        setNotification("No such document exists",DT_NOTIF_ERROR);
                        header("Location: ./");
                    }
                    while($stmt->fetch()){}
                    $stmt->free_result();
                    
                    $stmt2=$conn->prepare("SELECT id,description FROM documentcategory ORDER BY description ASC");
                    if($stmt2 === false) {
                        trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                    }
                    $stmt2->execute();
                    $stmt2->store_result();

                    if($stmt2->num_rows>0){
                        $stmt2->bind_result($uid,$uname);
                        while($stmt2->fetch()):
                            $category .= '<option value="'.$uid.'" '.($documentcategory==$uid?"selected='selected'":"").'>'.$uname.'</option>'; 
                        endwhile;
                    }
                    $stmt2->free_result();
                    $stmt->close();
                    
                    dbClose();
                }else{header("Location: ./");}
                
                displayHTMLPageHeader();?>
                <header><h1>Edit Document (#<?php printf("%08d",$tid); ?>)</h1></header>
                <article>
                <form action="./editdoc" method="post" data-ajax="false">
                    <input type="hidden" name="tid" value="<?php echo $tid; ?>"/>
                    <input type="hidden" name="barcodenumber" value="<?php echo $barcodenumber; ?>"/>
                    <label for="category-filter-menu">Document Category</label>
                    <select id="category-filter-menu" class="filterable-select" name="category-filter-menu" data-native-menu="false" required="true" data-inline="true" value="<?php echo (is_null($t=filter_input(INPUT_GET, 'category-filter-menu',FILTER_SANITIZE_NUMBER_INT))?"":$t); ?>" autofocus="true">
                        <?php echo $category; ?>
                    </select>
                    <label for="documentnumber">Document Number</label>
                    <input type="text" name="documentnumber" id="documentnumber" value="<?php echo $documentnumber; ?>"/>

                    <label for="remarks">Remarks</label>
                    <input type="text" name="remarks" id="remarks" value="<?php echo $remarks; ?>"/>

                    <input type="submit" value="Update" data-icon="refresh" data-ajax="false"/>
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
                $stmt=$conn->prepare("INSERT INTO document(documentnumber,remarks,author,barcodenumber,documentcategory) VALUES(?,?,?,?,?)");
                if($stmt === false) {
                    trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                }
                $userid=(isLoggedIn()?$_SESSION["uid"]:0);
                $postdocnumber=filter_input(INPUT_POST, "documentnumber", FILTER_SANITIZE_STRING);
                $postremarks=filter_input(INPUT_POST, "remarks",FILTER_SANITIZE_STRING);
                $postdoccategory=filter_input(INPUT_POST, "category-filter-menu",FILTER_SANITIZE_NUMBER_INT);
                $postbarcodenumber=filter_input(INPUT_POST, "barcodenumber",FILTER_SANITIZE_STRING);
                $stmt->bind_param('ssisi',$postdocnumber,$postremarks,$userid,$postbarcodenumber,$postdoccategory);
                $stmt->execute();
                $trackno = $stmt->insert_id;
                $stmt->close();

                $stmt2=$conn->prepare("INSERT INTO documentlog(docid,remarks,user) VALUES(?,?,?)");
                if($stmt2 === false) {
                    trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                }
                $msgremarks="Document added at ".$_SESSION["department"]." (".$_SESSION["section"].")";
                $stmt2->bind_param('isi',$trackno,$msgremarks,$userid);
                $stmt2->execute();
                $stmt->close();

                setNotification("Document was successfully added. Tracking number is <strong>".$postbarcodenumber."</strong>.");
                writeLog("Document ".$postbarcodenumber." has been added by ".$_SESSION["fullname"]."(".$_SESSION["uid"].").");
                dbClose();
                header("Location: ./?q=".$postbarcodenumber);
            }else{header("Location: ./");}
            break;
        case "editdoc":
            if(isLoggedIn() && checkPermission(DT_PERM_EDITDOC))
            {
                global $conn;
                dbConnect();
                $stmt=$conn->prepare("UPDATE document SET documentnumber=?, remarks=?, documentcategory=? WHERE id=?");
                if($stmt === false) {
                    trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                }
                $userid=(isLoggedIn()?$_SESSION["uid"]:0);
                $postdocnumber=filter_input(INPUT_POST, "documentnumber", FILTER_SANITIZE_STRING);
                $postremarks=filter_input(INPUT_POST, "remarks", FILTER_SANITIZE_STRING);
                $postdoccategory=filter_input(INPUT_POST, "category-filter-menu", FILTER_SANITIZE_NUMBER_INT);
                $tid=filter_input(INPUT_POST, "tid", FILTER_SANITIZE_NUMBER_INT);
                $postbarcodenumber=filter_input(INPUT_POST, "barcodenumber", FILTER_SANITIZE_STRING);
                $stmt->bind_param('ssii',$postdocnumber,$postremarks,$postdoccategory,$tid);
                $stmt->execute();
                $stmt->close();

                setNotification("Document $postdocnumber was successfully edited.");
                writeLog("Document ".$tid." has been updated to DocNo.=$postdocnumber, Remarks=$postremarks");
                dbClose();
                header("Location: ./?q=".$postbarcodenumber);
            }else{header("Location: ./");}
            break;
            case "receive":
            if(isLoggedIn() && checkPermission(DT_PERM_RECEIVEDOC))
            {
                if(!is_null(filter_input(INPUT_POST, "trackingnumber")))
                {
                    global $conn;
                    dbConnect();
                    $stmt=$conn->prepare("INSERT INTO documentlog(docid,remarks,user,visible) VALUES(?,?,?,?)");
                    if($stmt === false) {
                        trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                    }
                    $userid=(isLoggedIn()?$_SESSION["uid"]:0);
                    $posttrackingnumber=filter_input(INPUT_POST, "trackingnumber",FILTER_SANITIZE_NUMBER_INT);
                    $posttxtremarks=filter_input(INPUT_POST, "txtremarks",FILTER_SANITIZE_STRING);
                    $postfinalrelease=filter_input(INPUT_POST, "finalrelease",FILTER_SANITIZE_NUMBER_INT)+0;
                    $posthidden=abs(filter_input(INPUT_POST, "hiddenreceive",FILTER_SANITIZE_NUMBER_INT)-1);
                    $postbarcodenumber=filter_input(INPUT_POST, "barcodenumber",FILTER_SANITIZE_STRING);
                    $stmt->bind_param('isii',$posttrackingnumber,$posttxtremarks,$userid,$posthidden);
                    $stmt->execute();
                    
                    if($postfinalrelease==1)
                    {
                        $stmt=$conn->prepare("UPDATE document SET end=1 WHERE id=?");
                        if($stmt === false) {
                            trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                        }
                        $stmt->bind_param('i',$posttrackingnumber);
                        $stmt->execute();
                        setNotification("Document ".$postbarcodenumber." status has been released.");
                        writeLog("[END]Document ".$postbarcodenumber." was released at ".$_SESSION["department"]." (".$_SESSION["section"].").");
                    }
                    else {
                        setNotification("Document ".$postbarcodenumber." status has been updated.");
                        writeLog("Document ".$postbarcodenumber." was received at ".$_SESSION["department"]." (".$_SESSION["section"].").");
                    }
                    dbClose();
                    header("Location: ./?q=".$postbarcodenumber);
                }
                else
                {
                    header("Location: ./");
                }
            }else{header("Location: ./");}
            break;
        case "editreceive":
            if(isLoggedIn() && checkPermission(DT_PERM_EDITDOCTRACK))
            {
                if(!is_null(filter_input(INPUT_POST, "trackingnumber")))
                {
                    global $conn;
                    dbConnect();
                    $stmt=$conn->prepare("UPDATE documentlog SET remarks=?, visible=? WHERE logid=?");
                    if($stmt === false) {
                        trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                    }
                    $userid=(isLoggedIn()?$_SESSION["uid"]:0);
                    $trackingnumber = filter_input(INPUT_POST, "trackingnumber",FILTER_SANITIZE_NUMBER_INT);
                    $logid = filter_input(INPUT_POST, "logid",FILTER_SANITIZE_NUMBER_INT);
                    $posttxtremarks = filter_input(INPUT_POST, "txtremarks",FILTER_SANITIZE_STRING);
                    $postfinalrelease = filter_input(INPUT_POST, "finalrelease",FILTER_SANITIZE_NUMBER_INT);
                    $posthidden = abs(filter_input(INPUT_POST, "hiddenreceive",FILTER_SANITIZE_NUMBER_INT)-1);
                    $postbarcodenumber = filter_input(INPUT_POST, "barcodenumber",FILTER_SANITIZE_STRING);
                    $stmt->bind_param('sii',$posttxtremarks,$posthidden,$logid);
                    $stmt->execute();
                    
                    $stmt=$conn->prepare("UPDATE document SET end=? WHERE id=?");
                    if($stmt === false) {
                        trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                    }
                    $stmt->bind_param('ii',$postfinalrelease,$trackingnumber);
                    $stmt->execute();

                    setNotification("Receiving Remarks has been updated.");
                    writeLog("Document $postbarcodenumber receiving remarks was changed to \"".$posttxtremarks."\", final release = $postfinalrelease, hidden = $posthidden.");
                    dbClose();
                    header("Location: ./?q=".$postbarcodenumber);
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
                    
                    $stmt = $conn->prepare("SELECT fullname,department, division, section, permission FROM user WHERE uid=?");
                    $stmt->bind_param("i",$uid);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($fullname,$department,$division,$section,$permission);
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
                    
                    <label for="department">Division</label>
                    <input type="text" name="division" id="division" <?php echo $uid?'value="'.$division.'"':''; ?> />
                    
                    <label for="section">Section</label>
                    <input type="text" name="section" id="section" <?php echo $uid?'value="'.$section.'"':''; ?> />
                    
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
                        <input type="checkbox" name="p[]" id="checkbox07" value="64" <?php echo $uid?(checkPermission(DT_PERM_REPORT,$permission)?'checked="checked"':''):''; ?>/>
                        <label for="checkbox07">Reports</label>
                        <input type="checkbox" name="p[]" id="checkbox08" value="128" <?php echo $uid?(checkPermission(DT_PERM_ADVSEARCH,$permission)?'checked="checked"':''):''; ?>/>
                        <label for="checkbox08">Advanced Search</label>
                        <input type="checkbox" name="p[]" id="checkbox09" value="256" <?php echo $uid?(checkPermission(DT_PERM_HIDDENRECEIVE,$permission)?'checked="checked"':''):''; ?>/>
                        <label for="checkbox09">Hidden Receives</label>
                        <input type="checkbox" name="p[]" id="checkbox10" value="512" <?php echo $uid?(checkPermission(DT_PERM_REOPENDOC,$permission)?'checked="checked"':''):''; ?>/>
                        <label for="checkbox10">Reopen Document</label>
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
                    $stmt=$conn->prepare("INSERT INTO user(uid,password,fullname,department,division,section,permission) VALUES(?,?,?,?,?,?,?)");
                    if($stmt === false) {
                        trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                        break;
                    }
                    $userid=(isLoggedIn()?$_SESSION["uid"]:0);
                    $uid = filter_input(INPUT_POST, "uid");
                    $password=md5(filter_input(INPUT_POST, "password"));
                    $fullname=filter_input(INPUT_POST, "fullname");
                    $department=filter_input(INPUT_POST, "department");
                    $division=filter_input(INPUT_POST, "division");
                    $section=filter_input(INPUT_POST, "section");
                    $pcount=filter_input_array(INPUT_POST)["p"];
                    $permission=0;
                    while(list($key,$val)=@each($pcount)) {
                        $permission += intval($val);
                    }
                    
                    $stmt->bind_param('isssssi',$uid,$password,$fullname,$department,$division,$section,$permission);
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
                $division=filter_input(INPUT_POST, "division");
                $section=filter_input(INPUT_POST, "section");
                $pcount=filter_input_array(INPUT_POST)["p"];
                $permission=0;
                while(list($key,$val)=@each($pcount)) {
                    $permission += intval($val);
                }

                if(filter_input(INPUT_POST, "password")=="")
                {
                    $stmt=$conn->prepare("UPDATE user SET fullname=?, department=?, division=?, section=?, permission=? WHERE uid=?");
                    if($stmt === false) {
                        trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                        break;
                    }
                    $stmt->bind_param('ssssii',$fullname,$department,$division,$section,$permission,$uid);
                }else{
                    $stmt=$conn->prepare("UPDATE user SET fullname=?, password=?, department=?, division=?, section=?, permission=? WHERE uid=?");
                    if($stmt === false) {
                        trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                        break;
                    }
                    $stmt->bind_param('sssssii',$fullname,$password,$department,$division,$section,$permission,$uid);
                }
                $stmt->execute();
                setNotification("User ".$fullname."(".$uid.") has been updated.");
                writeLog("User ".$fullname."(".$uid.") has been updated to Name=".$fullname.", Dept=".$department.", Section=".$section.", Perm=".$permission.(filter_input(INPUT_POST, "password")==""?"":", Password=****************".substr($password,16)));
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
                    <table class="ui-responsive table-stripe" data-role="table" data-mode="columntoggle" id="tblUserList">
                        <thead>
                            <tr>
                                <th data-priority="2">Employee ID</th>
                                <th data-priority="1">Full Name</th>
                                <th data-priority="3">Department</th>
                                <th data-priority="4">Section</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php global $conn;
                            dbConnect();
                            $stmt=$conn->prepare("SELECT uid, fullname, department, division, section FROM user ORDER BY fullname");
                            if($stmt === false) {
                                trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                            }
                            $stmt->execute();
                            $stmt->store_result();
                            if($stmt->num_rows>0)
                            {
                                $stmt->bind_result($uid,$fullname,$department,$division,$section);
                                while($stmt->fetch()): ?>
                                <tr>
                                    <td><?php echo str_pad($uid,4,"0",STR_PAD_LEFT); ?></td>
                                    <td><?php echo $fullname; ?></td>
                                    <td><?php echo $department; ?></td>
                                    <td><?php echo $section; ?></td>
                                    <td><a href="./regform?id=<?php echo str_pad($uid,4,"0",STR_PAD_LEFT); ?>" data-role="button" data-icon="edit">Edit</a></td>
                                </tr>
                            <?php endwhile;
//                                $_SESSION['permlist']=  parsePermission($_SESSION['permission']);
//                                writeLog($_SESSION["fullname"]."(".$_SESSION["uid"].") logged in to the system.");
                            }
                            else
                            {
                                setNotification("No users.",DT_NOTIF_ERROR);
                            }
                            $stmt->close(); ?>
                        </tbody>
                    </table>
                </article>
                <script type="text/javascript">
                    $(document).ready(function() {
                        ul = $('#tblUserList').dataTable({
                        "order": [[1,"asc"]]});
                    
                        ulapi = ul.api();
                        //$("#tblUserList").on( "init.dt", function() {
                            //window.alert("test");
                            $("#tblUserList_wrapper").enhanceWithin();
                            $(".dataTables_wrapper div.ui-select>div.ui-btn").addClass("ui-btn-a");
                            $("#tblUserList_filter input").on("change",function(){
                                ulapi.search($(this).val()).draw();
                            });
                        //});
                        $("#tblUserList").on( "draw.dt", function() {
                            //window.alert("test");
                            $("#tblUserList_wrapper").enhanceWithin();
                            $(".dataTables_wrapper div.ui-select>div.ui-btn").addClass("ui-btn-a");
                            /*$("#tblUserList_filter input").on("change",function(){
                                ulapi.search($(this).val()).draw();
                            });*/
                        });
                    });
                    
                </script>
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
                        <table data-type="table" data-mode="columntoggle" class="ui-responsive table-stripe"  id="tbllog">
                            <thead>
                                <tr>
                                    <th data-priority="1">Timestamp</th>
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
                        "ajax": "./auditlogss",
                        "order": [[0,"desc"]]});
                    
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
        case "reports":
                $users="";
                $pagenames="";
                $category="";
                global $conn;
                dbConnect();
                $stmt2=$conn->prepare("SELECT uid,fullname FROM user ORDER BY fullname ASC");
                if($stmt2 === false) {
                    trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                }
                $stmt2->execute();
                $stmt2->store_result();

                if($stmt2->num_rows>0){
                    $stmt2->bind_result($uid,$uname);
                    while($stmt2->fetch()):
                        $users .= '<option value="'.$uid.'">'.$uname.'</option>'; 
                    endwhile;
                }
                $stmt2->free_result();
                
                $stmt2=$conn->prepare("SELECT DISTINCT page FROM auditlog");
                if($stmt2 === false) {
                    trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                }
                $stmt2->execute();
                $stmt2->store_result();

                if($stmt2->num_rows>0){
                    $stmt2->bind_result($pagename);
                    while($stmt2->fetch()):
                        $pagenames .= '<option value="'.$pagename.'">'.$pagename.'</option>'; 
                    endwhile;
                }
                $stmt2->free_result();
                
                $stmt2=$conn->prepare("SELECT id,description FROM documentcategory ORDER BY description ASC");
                if($stmt2 === false) {
                    trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                }
                $stmt2->execute();
                $stmt2->store_result();

                if($stmt2->num_rows>0){
                    $stmt2->bind_result($uid,$uname);
                    while($stmt2->fetch()):
                        $category .= '<option value="'.$uid.'">'.$uname.'</option>'; 
                    endwhile;
                }
                $stmt2->free_result();
                
                displayHTMLPageHeader();?>
                <header><h1>Reports</h1></header>
                <article>
                    <div data-role="collapsibleset" data-theme="d" data-content-theme="a" id='reportpanel' class='ui-corner-all'>
<!--                        <div data-role="collapsible">
                            <h3>Documents</h3>
                            <form action="./report?t=homeownerlist" method="post" target="_blank">
                                <fieldset data-role="collapsible" data-theme="a" data-inset="false">
                                    <legend>List of Documents</legend>
                                    <input type="submit" value="Generate" data-inline="true"/>
                                </fieldset>
                            </form>
                        </div>-->
                        <div data-role="collapsible" data-collapsed="false">
                            <h3>List of Reports</h3>
                            <form action="./report?t=doclist" method="post" target="_blank">
                                <fieldset data-role="collapsible" data-theme="a" data-inset="false">
                                    <legend>List of Documents</legend>
                                    
                                    <div class="ui-grid-d">
                                        <div class="ui-block-a">
                                            <label for="owner-filter-menu">Select User</label>
                                            <select id="owner-filter-menu" name="owner-filter-menu" data-native-menu="false" required="true" data-inline="true">
                                                <option value="-1">All users</option>
                                                <?php echo $users; ?>
                                            </select>
                                        </div>
                                        <div class="ui-block-b">
                                            <label for="category-filter-menu">Document Category</label>
                                            <select id="category-filter-menu" class="filterable-select" name="category-filter-menu" data-native-menu="false" required="true" data-inline="true" value="<?php echo (is_null($t=filter_input(INPUT_GET, 'category-filter-menu',FILTER_SANITIZE_NUMBER_INT))?"":$t); ?>">
                                                <option value="-1">All Categories</option>
                                                <?php echo $category; ?>
                                            </select>
                                        </div>
                                        <div class="ui-block-c">
                                            <label for="departmentd01">Department</label>
                                            <input type="text" id="departmentd01" name="department" placeholder="Department"/>
                                        </div>
                                        <div class="ui-block-d">
                                            <label for="divisiond01">Division</label>
                                            <input type="text" id="divisiond01" name="division" placeholder="Division"/>
                                        </div>
                                        <div class="ui-block-e">
                                            <label for="sectiond01">Section</label>
                                            <input type="text" id="sectiond01" name="section" placeholder="Section"/>
                                        </div>
                                    </div>
                                    
                                    <div class="ui-grid-a">
                                        <div class="ui-block-a">
                                            <label for="startdateh01" data-inline="true">From</label>
                                            <input type="date" data-inline="true" name="startdate" id="startdateh01" value="<?php echo date("Y-m-d"); ?>" placeholder="yyyy-mm-dd"/>
                                        </div>
                                        <div class="ui-block-b">
                                            <label for="enddateh01" data-inline="true">To</label>
                                            <input type="date" data-inline="true" name="enddate" id="enddateh01" value="<?php echo date("Y-m-d"); ?>" placeholder="yyyy-mm-dd"/>
                                        </div>
                                    </div>
                                    <label for="released">Release Status</label>
                                    <select id="release-menu" name="released" data-native-menu="false" required="true" data-inline="false">
                                        <option value="-1">All</option>
                                        <option value="0">Processing</option>
                                        <option value="1">Released</option>
                                    </select>
                                    <input type="submit" value="Generate" data-inline="true"/>
                                </fieldset>
                            </form>
                            
                            <form action="./report?t=receivelist" method="post" target="_blank">
                                <fieldset data-role="collapsible" data-theme="a" data-inset="false">
                                    <legend>Document Receives</legend>
                                    
                                    <div class="ui-grid-c">
                                        <div class="ui-block-a">
                                            <label for="owner-filter-menu2">Select User</label>
                                            <select id="owner-filter-menu2" name="owner-filter-menu" data-native-menu="false" required="true" data-inline="true">
                                                <option value="-1">All users</option>
                                                <?php echo $users; ?>
                                            </select>
                                        </div>
                                        <div class="ui-block-b">
                                            <label for="departmentd02">Department</label>
                                            <input type="text" id="departmentd02" name="department" placeholder="Department"/>
                                        </div>
                                        <div class="ui-block-c">
                                            <label for="divisiond02">Division</label>
                                            <input type="text" id="divisiond02" name="division" placeholder="Division"/>
                                        </div>
                                        <div class="ui-block-d">
                                            <label for="sectiond02">Section</label>
                                            <input type="text" id="sectiond02" name="section" placeholder="Section"/>
                                        </div>
                                    </div>
                                    
                                    <div class="ui-grid-a">
                                        <div class="ui-block-a">
                                            <label for="startdateh02" data-inline="true">From</label>
                                            <input type="date" data-inline="true" name="startdate" id="startdateh02" value="<?php echo date("Y-m-d"); ?>" placeholder="yyyy-mm-dd"/>
                                        </div>
                                        <div class="ui-block-b">
                                            <label for="enddateh02" data-inline="true">To</label>
                                            <input type="date" data-inline="true" name="enddate" id="enddateh02" value="<?php echo date("Y-m-d"); ?>" placeholder="yyyy-mm-dd"/>
                                        </div>
                                    </div>
                                    
                                    <input type="submit" value="Generate" data-inline="true"/>
                                </fieldset>
                            </form>
                            
                            <form action="./report?t=userlist" method="post" target="_blank">
                                <fieldset data-role="collapsible" data-theme="a" data-inset="false">
                                    <legend>User List</legend>
                                    <input type="submit" value="Generate" data-inline="true"/>
                                </fieldset>
                            </form>
                            
                            <form action="./report?t=auditlog" method="post" target="_blank">
                                <fieldset data-role="collapsible" data-theme="a" data-inset="false">
                                    <legend>Audit Log</legend>
                                    
                                    <div class="ui-grid-d">
                                        <div class="ui-block-a">
                                            <label for="owner-filter-menu4">Select User</label>
                                            <select id="owner-filter-menu4" name="owner-filter-menu" data-native-menu="false" required="true" data-inline="true">
                                                <option value="-1">All users</option>
                                                <?php echo $users; ?>
                                            </select>
                                        </div>
                                        <div class="ui-block-b">
                                            <label for="page-filter-menu4">Select Pages</label>
                                            <select id="page-filter-menu4" name="page-filter-menu" data-native-menu="false" data-inline="true">
                                                <option value="">All pages</option>
                                                <?php echo $pagenames; ?>
                                            </select>
                                        </div>
                                        <div class="ui-block-c">
                                            <label for="departmentd04">Department</label>
                                            <input type="text" id="departmentd04" name="department" placeholder="Department"/>
                                        </div>
                                        <div class="ui-block-d">
                                            <label for="divisiond04">Division</label>
                                            <input type="text" id="divisiond04" name="division" placeholder="Division"/>
                                        </div>
                                        <div class="ui-block-e">
                                            <label for="sectiond04">Section</label>
                                            <input type="text" id="sectiond04" name="section" placeholder="Section"/>
                                        </div>
                                    </div>
                                    
                                    <div class="ui-grid-a">
                                        <div class="ui-block-a">
                                            <label for="startdateh04" data-inline="true">From</label>
                                            <input type="date" data-inline="true" name="startdate" id="startdateh04" value="<?php echo date("Y-m-d"); ?>" placeholder="yyyy-mm-dd"/>
                                        </div>
                                        <div class="ui-block-b">
                                            <label for="enddateh04" data-inline="true">To</label>
                                            <input type="date" data-inline="true" name="enddate" id="enddateh04" value="<?php echo date("Y-m-d"); ?>" placeholder="yyyy-mm-dd"/>
                                        </div>
                                    </div>
                                    <input type="submit" value="Generate" data-inline="true"/>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                    
                    
                    
<!--                <form action="./report" method="post" data-ajax="false">
                    <label for="documentnumber">Document Number</label>
                    <input type="text" name="documentnumber" id="documentnumber"/>

                    <label for="remarks">Remarks</label>
                    <input type="text" name="remarks" id="remarks"/>

                    <input type="submit" value="Add" data-icon="plus" data-ajax="false"/>
                </form>-->
                </article>
                <?php displayHTMLPageFooter();
            break;
        case "report":
            if(isLoggedIn()){
                $resultset=array();
                $resultcolumns=array();
                $resultfooter=null;
                $resultclasses=array();
                $title="";
                $subtitle="";
                $msg="";

                global $conn;
                dbConnect();

                switch(filter_input(INPUT_GET, "t"))
                {
                    case "doclist":
                        $uid=filter_input(INPUT_POST, "owner-filter-menu",FILTER_SANITIZE_NUMBER_INT);
                        $cid=filter_input(INPUT_POST, "category-filter-menu",FILTER_SANITIZE_NUMBER_INT);
                        $department=filter_input(INPUT_POST, "department",FILTER_SANITIZE_STRING);
                        $division=filter_input(INPUT_POST, "division",FILTER_SANITIZE_STRING);
                        $section=filter_input(INPUT_POST, "section",FILTER_SANITIZE_STRING);
                        $startdate=filter_input(INPUT_POST, "startdate",FILTER_SANITIZE_STRING)." 00:00:00";
                        $enddate=filter_input(INPUT_POST, "enddate",FILTER_SANITIZE_STRING)." 23:59:59";
                        $released=filter_input(INPUT_POST, "released",FILTER_SANITIZE_NUMBER_INT);
                        
                        $title="List of Documents";
                        $msg="";
                        $sql="SELECT a.datecreated, a.barcodenumber, a.documentnumber,a.author,b.fullname,a.remarks,c.remarks,d.description,a.end FROM document a inner join (select docid, max(logid) as lid from documentlog group by docid) b ON b.docid=a.id INNER JOIN documentlog c on c.logid=b.lid INNER JOIN user b ON a.author=b.uid INNER JOIN documentcategory d ON a.documentcategory=d.id WHERE a.datecreated>=? AND a.datecreated<=?";
                        if($uid>0){
                            $sql.=" AND a.author=".$uid;
                        }
                        if($cid>=0){
                            $sql.=" AND a.documentcategory=".$cid;
                        }
                        if($department!=""){
                            $sql.=" AND b.department='".$department."'";
                        }
                        if($division!=""){
                            $sql.=" AND b.division='".$division."'";
                        }
                        if($section!=""){
                            $sql.=" AND b.section='".$section."'";
                        }
                        if($released>=0){
                            $sql.=" AND a.end=".$released;
                        }
                        $stmt=$conn->prepare($sql);
                        if($stmt === false) {
                            trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                        }
                        $resultcolumns = ["Date","Tracking No.","Document No.","User ID","Full Name","Remarks","Status","Document Type","Released"];
                        $resultclasses = ["","","","","","","","",""];
        //                $postusername=filter_input(INPUT_POST, "uid");
        //                $postpassword=md5(filter_input(INPUT_POST, "password"));
                        $stmt->bind_param('ss',$startdate,$enddate);
                        $stmt->execute();

                        $stmt->store_result();
                        if($stmt->num_rows>0)
                        {
                            $stmt->bind_result($datecreated,$trackingnumber,$documentnumber,$userid,$fullname,$remarks,$status,$doctype,$released);
                            while($stmt->fetch()){
                                $resultset[]=array($datecreated,$trackingnumber,$documentnumber,$userid,$fullname,$remarks,$status,$doctype,($released?"Yes":"No"));
                            }
                        }
                        break;
                    case "receivelist":
                        $uid=filter_input(INPUT_POST, "owner-filter-menu");
                        $department=filter_input(INPUT_POST, "department");
                        $division=filter_input(INPUT_POST, "division");
                        $section=filter_input(INPUT_POST, "section");
                        $startdate=filter_input(INPUT_POST, "startdate")." 00:00:00";
                        $enddate=filter_input(INPUT_POST, "enddate")." 23:59:59";
                        
                        $title="List of Received Documents";
                        $msg="";
                        $sql="SELECT a.ts,LPAD(a.docid,8,'0'),c.remarks,a.remarks,CONCAT(b.fullname,' (',a.user,')') FROM documentlog a INNER JOIN document c ON a.docid=c.id INNER JOIN user b ON a.user=b.uid WHERE a.ts>=? AND a.ts<=?";
                        if($uid>0){
                            $sql.=" AND a.user=".$uid;
                        }
                        if($department!=""){
                            $sql.=" AND b.department='".$department."'";
                        }
                        if($division!=""){
                            $sql.=" AND b.division='".$division."'";
                        }
                        if($section!=""){
                            $sql.=" AND b.section='".$section."'";
                        }
                        $stmt=$conn->prepare($sql);
                        if($stmt === false) {
                            trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                        }
                        $resultcolumns = ["Date","Tracking No.","Doc Desc.","Rec. Remarks","Received by"];
                        $resultclasses = ["","","","",""];
        //                $postusername=filter_input(INPUT_POST, "uid");
        //                $postpassword=md5(filter_input(INPUT_POST, "password"));
                        $stmt->bind_param('ss',$startdate,$enddate);
                        $stmt->execute();

                        $stmt->store_result();
                        if($stmt->num_rows>0)
                        {
                            $stmt->bind_result($timestamp,$trackingnumber,$docdetails,$remarks,$fullname);
                            while($stmt->fetch()){
                                $resultset[]=array($timestamp,$trackingnumber,$docdetails,$remarks,$fullname);
                            }
                        }
                        break;
                    case "userlist":
                        $title="List of Users";
                        $msg="";
                        $sql="SELECT uid, fullname, department, division, section, regdate FROM user";
                        
                        $stmt=$conn->prepare($sql);
                        if($stmt === false) {
                            trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                        }
                        $resultcolumns = ["Employee ID","Name","Department","Division","Section","Registration Date"];
                        $resultclasses = ["","","","","",""];
                        $stmt->execute();

                        $stmt->store_result();
                        if($stmt->num_rows>0)
                        {
                            $stmt->bind_result($uid,$fullname,$department,$division,$section,$regdate);
                            while($stmt->fetch()){
                                $resultset[]=array($uid,$fullname,$department,$division,$section,$regdate);
                            }
                        }
                        break;
                    case "auditlog":
                        $uid=filter_input(INPUT_POST, "owner-filter-menu");
                        $pagename=filter_input(INPUT_POST, "page-filter-menu");
                        $department=filter_input(INPUT_POST, "department");
                        $division=filter_input(INPUT_POST, "division");
                        $section=filter_input(INPUT_POST, "section");
                        $startdate=filter_input(INPUT_POST, "startdate")." 00:00:00";
                        $enddate=filter_input(INPUT_POST, "enddate")." 23:59:59";
                        
                        $title="Audit Log";
                        $msg="";
                        $sql="SELECT a.ts,CONCAT(b.fullname,' (',a.user,')'),a.page,a.msg FROM auditlog a INNER JOIN user b ON a.user=b.uid WHERE a.ts>=? AND a.ts<=?";
                        if($uid>0){
                            $sql.=" AND a.user=".$uid;
                        }
                        if($pagename!=""){
                            $sql.=" AND a.page='".$pagename."'";
                        }
                        if($department!=""){
                            $sql.=" AND b.department='".$department."'";
                        }
                        if($division!=""){
                            $sql.=" AND b.division='".$division."'";
                        }
                        if($section!=""){
                            $sql.=" AND b.section='".$section."'";
                        }
                        $stmt=$conn->prepare($sql);
                        if($stmt === false) {
                            trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                        }
                        $resultcolumns = ["Date","User","Page","Message"];
                        $resultclasses = ["","","",""];
        //                $postusername=filter_input(INPUT_POST, "uid");
        //                $postpassword=md5(filter_input(INPUT_POST, "password"));
                        $stmt->bind_param('ss',$startdate,$enddate);
                        $stmt->execute();

                        $stmt->store_result();
                        if($stmt->num_rows>0)
                        {
                            $stmt->bind_result($ts,$user,$page,$msg);
                            while($stmt->fetch()){
                                $resultset[]=array($ts,$user,$page,$msg);
                            }
                        }
                        break;
                }


                $stmt->close();
                dbClose();
                displayPlainHTMLPageHeader($title); echo "<!-- $sql -->"; ?>
                        <script type="text/javascript">
                            $(document).ready(function() {
                                rptbl=$('#tblreport').dataTable({
                                    paging:false,
                                    "footerCallback": function ( row, data, start, end, display ) {
                                        var api = this.api(), data;

                                        // Remove the formatting to get integer data for summation
                                        var intVal = function ( i ) {
                                            return typeof i === 'string' ?
                                                i.replace(/[\$,]/g, '')*1 :
                                                typeof i === 'number' ?
                                                    i : 0;
                                        };

                                        // Total over all pages
//                                        data = api.column( 4 ).data();
//                                        total = data.length ?
//                                            data.reduce( function (a, b) {
//                                                    return intVal(a) + intVal(b);
//                                            } ) :
//                                            0;

                                        // Total over this page
                                        for(i=0; i<this.fnSettings().aoColumns.length; i++)
                                        {
                                            data = api.column( i, { page: 'current'} ).data();
                                            pageTotal = data.length?(data.length===1?intVal(data[0]):data.reduce(function(a, b){return intVal(a)+intVal(b);})):0;
                                        
                                            // Update footer
//                                            if($(api.column(i).footer().className)==="textamount"){
                                                try{
                                                    $(api.column(i).footer()).filter(".total").html(numberWithCommas(pageTotal.toFixed(2)));
                                                }catch(e){}
//                                            }
                                        }

                                        
                                    }
                                });
                                var tableTools = new $.fn.dataTable.TableTools( rptbl, {
                                    "buttons": [
                                        "copy",
                                        "csv",
                                        "xls",
                                        "pdf",
                                        { "type": "print", "buttonText": "Print me!" }
                                    ],
                                    "sSwfPath":"./plugin/DataTables-1.10.0/extensions/TableTools/swf/copy_csv_xls_pdf.swf",
                                    "aButtons": [
                                        "copy",
                                        "csv",
                                        "xls",
                                        {
                                            "sExtends": "pdf",
                                            "sPdfOrientation": "landscape",
                                            "sPdfMessage": "<?php echo $msg; ?>"
                                        },
                                        "print"
                                    ]
                                } );

                                $( tableTools.fnContainer() ).appendTo('#ttools');
                            } );
                        </script>

                        <div class="soapage">
                            <div id="pagetitle">
                          <?php displayPrintHeader(); ?>
                          <h3><?php echo $title; ?></h3>
                          <div class="sub-title"><?php echo $subtitle; ?></div>
                            </div>
                          <div id="ttools"></div>
                          <table id="tblreport" width="100%" class="display">
                              <thead>
                                  <tr>
                                  <?php foreach($resultcolumns as $col): ?>
                                    <th><?php echo $col; ?></th>
                                  <?php endforeach; ?>
                                  </tr>
                              </thead>
                              <tbody>
                                <?php $i=0; foreach ($resultset as $row): ?>
                                    <tr>
                                        <?php $j=0; foreach($row as $cell): ?>
                                            <td class="<?php echo $resultclasses[$j]; ?>"><?php echo $cell; $j++; ?></td>
                                        <?php endforeach; $i++; ?>
                                    </tr>
                                <?php endforeach; ?>
                              </tbody>
                              <?php if(!is_null($resultfooter)): ?>
                              <tfoot>
                                  <tr>
                                      <?php $i=0; foreach($resultfooter as $foot): ?>
                                      <th class="<?php echo $resultclasses[$i]; ?>"><?php echo $foot; $i++; ?></th>
                                      <?php endforeach; ?>
                                  </tr>
                              </tfoot>
                              <?php endif; ?>
                          </table>
                          <footer><div class="gentimestamp">Generated on <?php date_default_timezone_set("Asia/Manila"); echo date('Y-m-d h:i:s A', time());?></div></footer>
                        </div>
                      </body>
                    </html>  
                <?php
            }
            break;
        case "search":
            $users="";
            $category="";
            global $conn;
            dbConnect();
            $stmt2=$conn->prepare("SELECT uid,fullname FROM user ORDER BY fullname ASC");
            if($stmt2 === false) {
                trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
            }
            $stmt2->execute();
            $stmt2->store_result();

            if($stmt2->num_rows>0){
                $stmt2->bind_result($uid,$uname);
                while($stmt2->fetch()):
                    $users .= '<option value="'.$uid.'">'.$uname.'</option>'; 
                endwhile;
            }
            $stmt2->free_result();
            
            
            $stmt2=$conn->prepare("SELECT id,description FROM documentcategory ORDER BY description ASC");
            if($stmt2 === false) {
                trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
            }
            $stmt2->execute();
            $stmt2->store_result();

            if($stmt2->num_rows>0){
                $stmt2->bind_result($uid,$uname);
                while($stmt2->fetch()):
                    $category .= '<option value="'.$uid.'">'.$uname.'</option>'; 
                endwhile;
            }
            $stmt2->free_result();
            displayHTMLPageHeader();
            ?>
                <h1>Advanced Search</h1>
                <form action="./search" method="get">
                    <div class="ui-body ui-body-a ui-corner-all">
                        <label for="q" class="ui-hidden-accessible">Search Term:</label>
                        <input type="search" name="q" id="q" placeholder="Enter Search Term" autofocus="true" value="<?php echo (is_null($t=filter_input(INPUT_GET, 'q',FILTER_SANITIZE_STRING))?"":$t); ?>" />

                        <div class="ui-grid-d">
                            <div class="ui-block-a">
                                <label for="owner-filter-menu">Select User</label>
                                <select id="owner-filter-menu" class="filterable-select" name="owner-filter-menu" data-native-menu="false" required="true" data-inline="true" value="<?php echo (is_null($t=filter_input(INPUT_GET, 'owner-filter-menu',FILTER_SANITIZE_NUMBER_INT))?"":$t); ?>">
                                    <option value="-1">All users</option>
                                    <?php echo $users; ?>
                                </select>
                            </div>
                            <div class="ui-block-b">
                                <label for="category-filter-menu">Document Category</label>
                                <select id="category-filter-menu" class="filterable-select" name="category-filter-menu" data-native-menu="false" required="true" data-inline="true" value="<?php echo (is_null($t=filter_input(INPUT_GET, 'category-filter-menu',FILTER_SANITIZE_NUMBER_INT))?"":$t); ?>">
                                    <option value="-1">All Categories</option>
                                    <?php echo $category; ?>
                                </select>
                            </div>
                            <div class="ui-block-c">
                                <label for="departmentd01">Department</label>
                                <input type="text" id="departmentd01" name="department" placeholder="Department" value="<?php echo (is_null($t=filter_input(INPUT_GET, 'department',FILTER_SANITIZE_STRING))?"":$t); ?>"/>
                            </div>
                            <div class="ui-block-d">
                                <label for="divisiond01">Division</label>
                                <input type="text" id="divisiond01" name="division" placeholder="Division" value="<?php echo (is_null($t=filter_input(INPUT_GET, 'division',FILTER_SANITIZE_STRING))?"":$t); ?>"/>
                            </div>
                            <div class="ui-block-e">
                                <label for="sectiond01">Section</label>
                                <input type="text" id="sectiond01" name="section" placeholder="Section" value="<?php echo (is_null($t=filter_input(INPUT_GET, 'section',FILTER_SANITIZE_STRING))?"":$t); ?>"/>
                            </div>
                        </div>

                        <div class="ui-grid-a">
                            <div class="ui-block-a">
                                <label for="startdateh01" data-inline="true">From</label>
                                <input type="date" data-inline="true" name="startdate" id="startdateh01" value="<?php echo (is_null($t=filter_input(INPUT_GET, 'startdate',FILTER_SANITIZE_STRING))?date("Y-m-d"):$t); ?>" placeholder="yyyy-mm-dd"/>
                            </div>
                            <div class="ui-block-b">
                                <label for="enddateh01" data-inline="true">To</label>
                                <input type="date" data-inline="true" name="enddate" id="enddateh01" value="<?php echo (is_null($t=filter_input(INPUT_GET, 'enddate',FILTER_SANITIZE_STRING))?date("Y-m-d"):$t); ?>" placeholder="yyyy-mm-dd"/>
                            </div>
                        </div>
                        <input type="submit" name="act" value="Search" data-inline="true" data-icon="search"/>
                        <input type="hidden" name="p" value="<?php echo (is_null($t=filter_input(INPUT_GET, 'p',FILTER_SANITIZE_NUMBER_INT))?"1":$t); ?>"/>
                    </div>
                </form>
            <?php
            if(!is_null(filter_input(INPUT_GET, 'act'))):
                $resultset = array();
                $q=filter_input(INPUT_GET, "q",FILTER_SANITIZE_STRING);
                $uid=filter_input(INPUT_GET, "owner-filter-menu",FILTER_SANITIZE_NUMBER_INT);
                $catid=filter_input(INPUT_GET, "category-filter-menu",FILTER_SANITIZE_NUMBER_INT);
                $department=filter_input(INPUT_GET, "department",FILTER_SANITIZE_STRING);
                $division=filter_input(INPUT_GET, "division",FILTER_SANITIZE_STRING);
                $section=filter_input(INPUT_GET, "section",FILTER_SANITIZE_STRING);
                $startdate=filter_input(INPUT_GET, "startdate",FILTER_SANITIZE_STRING)." 00:00:00";
                $enddate=filter_input(INPUT_GET, "enddate",FILTER_SANITIZE_STRING)." 23:59:59";
                $pageno=filter_input(INPUT_GET, "p",FILTER_SANITIZE_NUMBER_INT);
                $pagesize=10;

                $sql="SELECT SQL_CALC_FOUND_ROWS a.datecreated, a.barcodenumber, a.documentnumber,a.author,b.fullname,a.remarks,c.remarks FROM document a inner join (select docid, max(logid) as lid from documentlog group by docid) b USING(id) INNER JOIN documentlog c on c.logid=b.lid INNER JOIN user b ON a.author=b.uid INNER JOIN documentcategory d ON d.id=a.documentcategory WHERE a.datecreated>=? AND a.datecreated<=? AND MATCH(a.barcodenumber,a.remarks) AGAINST (?)";
                if($uid>0){
                    $sql.=" AND a.author=".$uid;
                }
                if($catid>0){
                    $sql.=" AND a.documentcategory=".$catid;
                }
                if($department!=""){
                    $sql.=" AND b.department='".$department."'";
                }
                if($division!=""){
                    $sql.=" AND b.division='".$division."'";
                }
                if($section!=""){
                    $sql.=" AND b.section='".$section."'";
                }
                
                $sql.=" LIMIT ".(($pageno-1)*$pagesize).", ".$pagesize;
                $stmt=$conn->prepare($sql);
                if($stmt === false) {
                    trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                }
                //$resultcolumns = ["Date","Barcode No.","Document No.","User ID","Full Name","Remarks","Status"];
                //$resultclasses = ["","","","","","",""];
//                $postusername=filter_input(INPUT_POST, "uid");
//                $postpassword=md5(filter_input(INPUT_POST, "password"));
                $stmt->bind_param('sss',$startdate,$enddate,$q);
                $stmt->execute();

                $stmt->store_result();
                if($stmt->num_rows>0)
                {
                    $stmt->bind_result($datecreated,$barcodenumber,$documentnumber,$userid,$fullname,$remarks,$status);
                    while($stmt->fetch()){
                        $resultset[]=array($datecreated,$barcodenumber,$documentnumber,$userid,$fullname,$remarks,$status);
                    }
                }
                $stmt2->free_result();
                
                $sql = "SELECT FOUND_ROWS();";
                $stmt=$conn->prepare($sql);
                if($stmt === false) {
                    trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                }
                $stmt->execute();
                $stmt->store_result();
                if($stmt->num_rows>0)
                {
                    $stmt->bind_result($totalrows);
                    while($stmt->fetch()){
                    }
                }
                $stmt2->free_result();
                $lastpage=ceil($totalrows/$pagesize);
                
            ?>
                <div data-role="collapsible" data-collapsed="false">
                    <h4>Search Results <em>(<?php echo $totalrows; ?> results found)</em></h4>
                    <ul data-role="listview" data-inset="true" data-theme="a">
            <?php foreach($resultset as &$r):?>
                        <li class="ui-grid-a">
                            <a href="./?q=<?php echo $r[1]; ?>">
                                <div class="ui-block-a"><div class="barcodeTarget"><?php echo $r[1]; ?></div></div>
                                <div class="ui-block-b">
                                    <p class="ui-li-aside"><strong><?php echo $r[0]; ?></strong></p>
                                    <h4><?php echo $r[2]; ?></h4>
                                    <p><?php echo $r[5]; ?></p>
                                    <p><em><?php echo $r[6]; ?></em></p>
                                </div>
                            </a>
                        </li>
            <?php endforeach; ?>
                    </ul>
                    <div>
                        <div data-role="controlgroup" data-type="horizontal">
                            <a href="./search?<?php echo getPrevPageStr();?>" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext <?php echo ($pageno<=1?"ui-disabled":""); ?>">Previous</a>
                            <span class="ui-corner-all ui-btn-inline ui-btn"><?php echo "Page ".$pageno." of ".$lastpage; ?></span>
                            <a href="./search?<?php echo getNextPageStr();?>" class="ui-btn ui-corner-all ui-icon-carat-r ui-btn-icon-notext <?php echo ($pageno>=$lastpage?"ui-disabled":""); ?>">Next</a>
                        </div>
                    </div>
                </div>
            <?php
            endif;
            displayHTMLPageFooter();
            break;
        case "reopen":
            if(isLoggedIn() && checkPermission(DT_PERM_REOPENDOC))
            {
                if(!is_null(filter_input(INPUT_POST, "docid")))
                {
                    global $conn;
                    dbConnect();
                    $stmt=$conn->prepare("UPDATE document SET end=0 WHERE id=?");
                    if($stmt === false) {
                        trigger_error('<strong>Error:</strong> '.$conn->error, E_USER_ERROR);
                    }
                    $userid=(isLoggedIn()?$_SESSION["uid"]:0);
                    $docid=filter_input(INPUT_POST, "docid");
                    $barcodenumber=filter_input(INPUT_POST, "barcodenumber");
                    $stmt->bind_param('i',$docid);
                    $stmt->execute();

                    setNotification("Document $barcodenumber has been re-opened.");
                    writeLog("Document ".$barcodenumber." has been reopened.");
                    dbClose();
                    header("Location: ./?q=".$barcodenumber);
                }
                else
                {
                    header("Location: ./");
                }
            }else{header("Location: ./");}
            break;
        default :
            displayHTMLPageHeader();
            
                
            ?>
                <div class="ui-body ui-body-a ui-corner-all">
                    <form action="./" method="get">
                        <div data-role="controlgroup" data-type="horizontal" id="searchform">
                          <label for="q" class="ui-hidden-accessible">Search for Tracking Number</label>
                          <input type="search" name="q" id="q" placeholder="Enter Tracking Number" autofocus="true" data-wrapper-class="controlgroup-textinput ui-btn" value="<?php echo (isset($_GET['q'])?$_GET['q']:""); ?>" onfocus="$(this).select();"/>
                                <input type="submit" data-icon="search" value="Search" data-iconpos="notext"/>
                            </div>
                    </form>
                </div>
            <?php
            displaySearchResult();
            displayHTMLPageFooter();
    }
}
?>