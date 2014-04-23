<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
$info=array();
$qstr='';
if($email && $_REQUEST['a']!='add'){
    $title='Update Email';
    $action='update';
    $submit_text='Save Changes';
    $info=$email->getInfo();
    $info['id']=$email->getId();
    if($info['mail_delete'])
        $info['postfetch']='delete';
    elseif($info['mail_archivefolder'])
        $info['postfetch']='archive';
    else
        $info['postfetch']=''; //nothing.
    if($info['userpass'])
        $passwdtxt='To change password enter new password above.';

    $qstr.='&id='.$email->getId();
}else {
    $title='Add New Email';
    $action='create';
    $submit_text='Submit';
    $info['ispublic']=isset($info['ispublic'])?$info['ispublic']:1;
    $info['ticket_auto_response']=isset($info['ticket_auto_response'])?$info['ticket_auto_response']:1;
    $info['message_auto_response']=isset($info['message_auto_response'])?$info['message_auto_response']:1;
    $qstr.='&a='.$_REQUEST['a'];
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<h2>Email Address</h2>
<form action="emails.php?<?php echo $qstr; ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><strong>Email Information &amp; Settings</strong></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="180" class="required">
                Email Address
            </td>
            <td class="form-group form-inline has-error">
                <input class="form-control" type="text" size="35" name="email" value="<?php echo $info['email']; ?>">
                <?php if($errors['email']) echo '<span class="alert alert-danger">' .$errors['email']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                Email Name
            </td>
            <td class="form-group form-inline has-error">
                <input class="form-control" type="text" size="35" name="name" value="<?php echo $info['name']; ?>">
                <?php if($errors['name']) echo '<span class="alert alert-danger">' .$errors['name']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">
                New Ticket Priority
            </td>
            <td class="form-group form-inline">
                <select class="form-control" name="priority_id">
                    <option value="0" selected="selected">&mdash; System Default &mdash;</option>
                    <?php
                    $sql='SELECT priority_id, priority_desc FROM '.PRIORITY_TABLE.' pri ORDER by priority_urgency DESC';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while(list($id,$name)=db_fetch_row($res)){
                            $selected=($info['priority_id'] && $id==$info['priority_id'])?'selected="selected"':'';
                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$name);
                        }
                    }
                    ?>
                </select>
                <?php if($errors['priority_id']) echo '<span class="alert alert-danger">' .$errors['priority_id']. '</span>'; ?>
            </td
        </tr>
        <tr>
            <td width="180">
                New Ticket Dept.
            </td>
            <td class="form-group form-inline">
                <select class="form-control" name="dept_id">
                    <option value="0" selected="selected">&mdash; System Default &mdash;</option>
                    <?php
                    $sql='SELECT dept_id, dept_name FROM '.DEPT_TABLE.' dept ORDER by dept_name';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while(list($id,$name)=db_fetch_row($res)){
                            $selected=($info['dept_id'] && $id==$info['dept_id'])?'selected="selected"':'';
                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$name);
                        }
                    }
                    ?>
                </select>
                <?php if($errors['dept_id']) echo '<span class="alert alert-danger">' .$errors['dept_id']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">
                Auto-response
            </td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="noautoresp" value="1" <?php echo $info['noautoresp']?'checked="checked"':''; ?> >
                <label>Disable new ticket auto-response for this
                email. Override global and dept. settings.</label>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Login Information:</strong>: Optional BUT required when IMAP/POP or SMTP (with auth.) are enabled.</em>
            </th>
        </tr>
        <tr>
            <td width="180">
                Username
            </td>
            <td class="form-group form-inline">
                <input class="form-control" type="text" size="35" name="userid" value="<?php echo $info['userid']; ?>"
                    autocomplete="off" autocorrect="off">
                <?php if($errors['userid']) echo '<span class="alert alert-danger">' .$errors['userid']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">
               Password
            </td>
            <td class="form-group form-inline">
                <input class="form-control" type="password" size="35" name="passwd" value="<?php echo $info['passwd']; ?>"
                    autocomplete="off">
                <?php if($errors['passwd']) echo '<span class="alert alert-danger">' .$errors['passwd']. '</span>'; ?>
                <br><em><?php echo $passwdtxt; ?></em>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Mail Account</strong>: Optional setting for fetching incoming emails. Mail fetching must be enabled with autocron active or external cron setup. 
                <?php if($errors['mail']) echo '<span class="alert alert-danger">' .$errors['mail']; ?></font></em>
            </th>
        </tr>
        <tr><td>Status</td>
            <td class="form-group form-inline">
                <label><input class="form-control radio" type="radio" name="mail_active"  value="1"   <?php echo $info['mail_active']?'checked="checked"':''; ?> />Enable</label>
                <label><input class="form-control radio" type="radio" name="mail_active"  value="0"   <?php echo !$info['mail_active']?'checked="checked"':''; ?> />Disable</label>
                <?php if($errors['mail_active']) echo '<span class="alert alert-danger">' .$errors['mail_active']. '</span>'; ?>
            </td>
        </tr>
        <tr><td>Host</td>
            <td class="form-group form-inline">
            <input class="form-control" type="text" name="mail_host" size=35 value="<?php echo $info['mail_host']; ?>">
            <?php if($errors['mail_host']) echo '<span class="alert alert-danger">' .$errors['mail_host']. '</span>'; ?>
            </td>
        </tr>
        <tr><td>Port</td>
            <td class="form-group form-inline">
            <input class="form-control" type="text" name="mail_port" size=6 value="<?php echo $info['mail_port']?$info['mail_port']:''; ?>">
            <?php if($errors['mail_port']) echo '<span class="alert alert-danger">' .$errors['mail_port']. '</span>'; ?>
            </td>
        </tr>
        <tr><td>Protocol</td>
            <td class="form-group form-inline">
                <select class="form-control" name="mail_protocol">
                    <option value='POP'>&mdash; Select Mail Protocol &mdash;</option>
                    <option value='POP' <?php echo ($info['mail_protocol']=='POP')?'selected="selected"':''; ?> >POP</option>
                    <option value='IMAP' <?php echo ($info['mail_protocol']=='IMAP')?'selected="selected"':''; ?> >IMAP</option>
                </select>
                <?php if($errors['mail_protocol']) echo '<span class="alert alert-danger">' .$errors['mail_protocol']. '</span>'; ?>
            </td>
        </tr>

        <tr><td>Encryption</td>
            <td class="form-group form-inline">
                <select class="form-control" name="mail_encryption">
                    <option value='NONE'>None</option>
                    <option value='SSL' <?php echo ($info['mail_encryption']=='SSL')?'selected="selected"':''; ?> >SSL</option>
                </select>
                <?php if($errors['mail_encryption']) echo '<span class="alert alert-danger">' .$errors['mail_encryption']. '</span>'; ?>
            </td>
        </tr>
        <tr><td>Fetch Frequency</td>
            <td class="form-group form-inline">
                <input class="form-control" type="text" name="mail_fetchfreq" size="4" value="<?php echo $info['mail_fetchfreq']?$info['mail_fetchfreq']:''; ?>">
                <label>Delay intervals in minutes</label>
                <?php if($errors['mail_fetchfreq']) echo '<span class="alert alert-danger">' .$errors['mail_fetchfreq']. '</span>'; ?>
            </td>
        </tr>
        <tr><td>Emails Per Fetch</td>
            <td class="form-group form-inline">
                <input class="form-control" type="text" name="mail_fetchmax" size="4" value="<?php echo $info['mail_fetchmax']?$info['mail_fetchmax']:''; ?>"> 
                <label>Maximum emails to process per fetch.</label>
                <?php if($errors['mail_fetchmax']) echo '<span class="alert alert-danger">' .$errors['mail_fetchmax']. '</span>'; ?>
            </td>
        </tr>
        <tr><td valign="top">Fetched Emails</td>
             <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="postfetch" value="archive" <?php echo ($info['postfetch']=='archive')? 'checked="checked"': ''; ?> >
                 <label>Move to:</label> <input class="form-control" type="text" name="mail_archivefolder" size="20" value="<?php echo $info['mail_archivefolder']; ?>"/><label>folder.</label>
                 <?php if($errors['mail_folder']) echo '<span class="alert alert-danger">' .$errors['mail_folder']. '</span>'; ?>
                <input class="form-control radio" type="radio" name="postfetch" value="delete" <?php echo ($info['postfetch']=='delete')? 'checked="checked"': ''; ?> >
                <label>Delete fetched emails</label>
                <input  class="form-control radio" type="radio" name="postfetch" value="" <?php echo (isset($info['postfetch']) && !$info['postfetch'])? 'checked="checked"': ''; ?> >
                 <label>Do nothing (Not recommended)</label>
              <br><em>Moving fetched emails to a backup folder is highly recommended.</em> 
              <?php if($errors['postfetch']) echo '<span class="alert alert-danger">' .$errors['postfetch']. '</span>'; ?>
            </td>
        </tr>

        <tr>
            <th colspan="2">
                <em><strong>SMTP Settings</strong>: When enabled the <b>email account</b> will use SMTP server instead of internal PHP mail() function for outgoing emails.</em> 
                <?php if($errors['smtp']) echo '<span class="alert alert-danger">' .$errors['smtp']. '</span>'; ?>
            </th>
        </tr>
        <tr><td>Status</td>
            <td class="form-group form-inline">
                <label><input class="form-control radio" type="radio" name="smtp_active"  value="1"   <?php echo $info['smtp_active']?'checked':''; ?> />Enable</label>
                <label><input class="form-control radio" type="radio" name="smtp_active"  value="0"   <?php echo !$info['smtp_active']?'checked':''; ?> />Disable</label>
                <?php if($errors['smtp_active']) echo '<span class="alert alert-danger">' .$errors['smtp_active']. '</span>'; ?>
            </td>
        </tr>
        <tr><td>SMTP Host</td>
            <td class="form-group form-inline">
            <input class="form-control" type="text" name="smtp_host" size=35 value="<?php echo $info['smtp_host']; ?>">
            <?php if($errors['smtp_host']) echo '<span class="alert alert-danger">' .$errors['smtp_host']. '</span>'; ?>
            </td>
        </tr>
        <tr><td>SMTP Port</td>
            <td class="form-group form-inline">
            <input class="form-control" type="text" name="smtp_port" size=6 value="<?php echo $info['smtp_port']?$info['smtp_port']:''; ?>">
            <?php if($errors['smtp_port']) echo '<span class="alert alert-danger">' .$errors['smtp_port']. '</span>'; ?>
            </td>
        </tr>
        <tr><td>Authentication Required?</td>
            <td class="form-group form-inline">
                 <label>
                 <input class="form-control radio" type="radio" name="smtp_auth"  value="1"
                    <?php echo $info['smtp_auth']?'checked':''; ?> />Yes</label>
                 <label><input class="form-control radio" type="radio" name="smtp_auth"  value="0"
                    <?php echo !$info['smtp_auth']?'checked':''; ?> />NO</label>
                <?php if($errors['smtp_auth']) echo '<span class="alert alert-danger">' .$errors['smtp_auth']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td>Allow Header Spoofing?</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="smtp_spoofing" value="1" <?php echo $info['smtp_spoofing'] ?'checked="checked"':''; ?>>
                <label>Allow email header spoofing (only applies to emails being sent through this account)</label>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Internal Notes</strong>: Admin's notes.</em>
                <?php if($errors['notes']) echo '<span class="alert alert-danger">' .$errors['notes']. '</span>'; ?>
            </th>
        </tr>
        <tr>
            <td colspan="2">
                <textarea class="richtext no-bar" name="notes" cols="21"
                    rows="5" style="width: 60%;"><?php echo $info['notes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p class="centered">
    <input class="btn btn-success" type="submit" name="submit" value="<?php echo $submit_text; ?>">
    <input class="btn btn-warning" type="reset"  name="reset"  value="Reset">
    <input class="btn btn-danger" type="button" name="cancel" value="Cancel" onclick='window.location.href="emails.php"'>
</p>
</form>
