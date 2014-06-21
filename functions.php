<?php

function getLoginPage()
{
?>
<section>
  <header><h1>Login</h1></header>
  <div>
    <form action="login.php" method="post">
      <ul>
        <li>
          <label for="uid">ID Number</label>
          <input type="text" name="uid" id="uid"/>
        </li>
        <li>
          <label for="password">Password</label>
          <input type="password" name="password" id="password"/>
        </li>
        <li><input type="submit"/></li>
      </ul>
    </form>
  </div>
</section>
<?php
}

?>
