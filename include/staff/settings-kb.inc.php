<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');
?>
<h2>Knowledge Base Settings and Options</h2>
<form action="settings.php?t=kb" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="kb" >
<table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>Knowledge Base Settings</h4>
                <em>Disabling knowledge base disables clients' interface.</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="180">Knowledge base status:</td>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="enable_kb" value="1" <?php echo $config['enable_kb']?'checked="checked"':''; ?>>
              <label> Enable Knowledge base&nbsp;<em>(Client interface)</em></label>
              <?php if($errors['enable_kb']) echo '<span class="alert alert-danger">' .$errors['enable_kb']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">Canned Responses:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="enable_premade" value="1" <?php echo $config['enable_premade']?'checked="checked"':''; ?> >
                <label> Enable canned responses&nbsp;<em>(Available on ticket reply)</em></label>
                <?php if($errors['enable_premade']) echo '<span class="alert alert-danger">' .$errors['enable_premade']. '</span>'; ?>
            </td>
        </tr>
    </tbody>
</table>
<p class="centered">
    <input class="btn btn-success" type="submit" name="submit" value="Save Changes">
    <input class="btn btn-warning" type="reset" name="reset" value="Reset Changes">
</p>
</form>
