<?php
include_once(INCLUDE_DIR.'staff/login.header.php');
defined('OSTSCPINC') or die('Invalid path');
$info = ($_POST && $errors)?Format::htmlchars($_POST):array();
?>

<div id="loginBox">
    <h1 id="logo"><a href="index.php">osTicket Staff Password Reset</a></h1>
    <h3>A confirmation email has been sent</h3>
    <h3 style="color:black;"><em>
    A password reset email was sent to the email on file for your account.
    Follow the link in the email to reset your password.
    </em></h3>

    <form action="index.php" method="get">
        <input class="btn btn-primary" type="submit" name="submit" value="Login"/>
    </form>
</div>

</body>
</html>
