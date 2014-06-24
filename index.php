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
            $stmt=$conn->prepare("SELECT uid, fullname, department, section FROM user WHERE uid=? AND password=?");
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
                $stmt->bind_result($_SESSION['uid'],$_SESSION['fullname'],$_SESSION['department'],$_SESSION['section']);
                while($stmt->fetch()){}
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
            if(isLoggedIn())
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
            if(isLoggedIn())
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
            if(isLoggedIn())
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
            if(isLoggedIn())
            {
                displayHTMLPageHeader();?>
                <header><h1>User Registration</h1></header>
                <article>
                <form action="./regUser" method="post" data-ajax="false">
                    <label for="uid">Employee ID Number</label>
                    <input type="text" name="uid" id="uid"/>

                    <label for="fullname">Full Name</label>
                    <input type="text" name="fullname" id="fullname"/>
                    
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password"/>
                    
                    <label for="password2">Confirm Password</label>
                    <input type="password" name="password2" id="password2"/>
                    
                    <label for="department">Department</label>
                    <input type="text" name="department" id="department"/>
                    
                    <label for="section">Section</label>
                    <input type="text" name="section" id="section"/>

                    <input type="submit" value="Register" data-icon="edit" data-ajax="false"/>
                </form>
                </article>
                <?php displayHTMLPageFooter();
            }else{header("Location: ./");}
            break;
        default :
            displayHTMLPageHeader();
            displayHTMLPageFooter();
    }
}