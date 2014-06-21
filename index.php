<?php
require_once 'header.php';
if(isset($_SESSION['idnumber'])):
?>


<?php
else:
  getLoginPage();
endif;
require_once 'footer.php';
?>