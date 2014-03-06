<?php
include_once(INCLUDE_DIR.'staff/login.header.php');
defined('OSTSCPINC') or die('Invalid path');
$info = ($_POST && $errors)?Format::htmlchars($_POST):array();
?>

<div id="loginBox">
    <h1 id="logo"><a href="index.php">osTicket Staff Password Reset</a></h1>
    <h3><?php echo Format::htmlchars($msg); ?></h3>
    <form action="pwreset.php" method="post">
        <?php csrf_token(); ?>
        <input type="hidden" name="do" value="sendmail">
        <fieldset>
            <input class="form-control" type="text" name="userid" id="name" value="<?php echo
                $info['userid']; ?>" placeholder="username" autocorrect="off"
                autocapitalize="off">
        </fieldset>
        <input class="btn btn-primary" type="submit" name="submit" value="Send Email"/>
    </form>

</div>
</body>
</html>
