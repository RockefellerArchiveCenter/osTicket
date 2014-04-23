<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');
?>
<h2>Email Settings and Options</h2>
<form action="settings.php?t=emails" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="emails" >
<table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>Email Settings</h4>
                <em>Note that some of the global settings can be overridden at department/email level.</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="180" class="required">Default Email Templates:</td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="default_template_id">
                    <option value="">&mdash; Select Default Template &mdash;</option>
                    <?php
                    $sql='SELECT tpl_id,name FROM '.EMAIL_TEMPLATE_GRP_TABLE.' WHERE isactive=1 ORDER BY name';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while (list($id, $name) = db_fetch_row($res)){
                            $selected = ($config['default_template_id']==$id)?'selected="selected"':''; ?>
                            <option value="<?php echo $id; ?>"<?php echo $selected; ?>><?php echo $name; ?></option>
                        <?php
                        }
                    } ?>
                </select>
                <?php if($errors['default_template_id']) echo '<span class="alert alert-danger">' .$errors['default_template_id']. '</span>'; ?>
                <i class="help-tip icon-question-sign" href="#default_templates"></i>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">Default System Email:</td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="default_email_id">
                    <option value=0 disabled>Select One</option>
                    <?php
                    $sql='SELECT email_id,email,name FROM '.EMAIL_TABLE;
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while (list($id,$email,$name) = db_fetch_row($res)){
                            $email=$name?"$name &lt;$email&gt;":$email;
                            ?>
                            <option value="<?php echo $id; ?>"<?php echo ($config['default_email_id']==$id)?'selected="selected"':''; ?>><?php echo $email; ?></option>
                        <?php
                        }
                    } ?>
                 </select>
                 <?php if($errors['default_email_id']) echo '<span class="alert alert-danger">' .$errors['default_email_id']. '</span>'; ?>
                <i class="help-tip icon-question-sign" href="#default_email"></i>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">Default Alert Email:</td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="alert_email_id">
                    <option value="0" selected="selected">Use Default System Email (above)</option>
                    <?php
                    $sql='SELECT email_id,email,name FROM '.EMAIL_TABLE.' WHERE email_id != '.db_input($config['default_email_id']);
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while (list($id,$email,$name) = db_fetch_row($res)){
                            $email=$name?"$name &lt;$email&gt;":$email;
                            ?>
                            <option value="<?php echo $id; ?>"<?php echo ($config['alert_email_id']==$id)?'selected="selected"':''; ?>><?php echo $email; ?></option>
                        <?php
                        }
                    } ?>
                 </select>
                 <?php if($errors['alert_email_id']) echo '<span class="alert alert-danger">' .$errors['alert_email_id']. '</span>'; ?>
                <i class="help-tip icon-question-sign" href="#default_alert_email"></i>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">Admin's Email Address:</td>
            <td class="form-group form-inline has-error">
                <input class="form-control" type="text" size=40 name="admin_email" value="<?php echo $config['admin_email']; ?>">
                 <?php if($errors['admin_email']) echo '<span class="alert alert-danger">' .$errors['admin_email']. '</span>'; ?>
                <i class="help-tip icon-question-sign" href="#admin_email"></i>
            </td>
        </tr>
        <tr><th colspan=2><em><strong>Incoming Emails:</strong>&nbsp;
            <i class="help-tip icon-question-sign" href="#incoming_email"></i>
            </em></th>
        <tr>
            <td width="180">Email Polling:</td>
            <td class="form-group form-inline">
            <input class="form-control checkbox" type="checkbox" name="enable_mail_polling" value=1 <?php echo $config['enable_mail_polling']? 'checked="checked"': ''; ?>  > 
            <label>Enable POP/IMAP polling</label>
                <i class="help-tip icon-question-sign" href="#enable_email_poll"></i>
                 <input type="checkbox" name="enable_auto_cron" <?php echo $config['enable_auto_cron']?'checked="checked"':''; ?>>
                 <label>Poll on auto-cron</label>
                <i class="help-tip icon-question-sign" href="#enable_autocron_poll"></i>
            </td>
        </tr>
        <tr>
            <td width="180">Strip Quoted Reply:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="strip_quoted_reply" <?php echo $config['strip_quoted_reply'] ? 'checked="checked"':''; ?>>
                <label>(depends on the reply separator tag set below)</label>
                <?php if($errors['strip_quoted_reply']) echo '<span class="alert alert-danger">' .$errors['strip_quoted_reply']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">Reply Separator Tag:</td>
            <td class="form-group form-inline">
            <input class="form-control" type="text" name="reply_separator" value="<?php echo $config['reply_separator']; ?>">
                <?php if($errors['reply_separator']) echo '<span class="alert alert-danger">' .$errors['reply_separator']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">Emailed Tickets Priority:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="use_email_priority" value="1" <?php echo $config['use_email_priority'] ?'checked="checked"':''; ?> >
                <label>Use email priority when available</label>
                <i class="help-tip icon-question-sign" href="#use_email_priority"></i>
            </td>
        </tr>
        <tr>
            <td width="180">Accept Email Collaborators:</td>
            <td class="form-group form-inline">
            <input class="form-control checkbox" type="checkbox" name="add_email_collabs" <?php
    echo $config['add_email_collabs'] ? 'checked="checked"' : ''; ?>/>
            <label>Automatically add collaborators from email fields&nbsp;</label>
            <i class="help-tip icon-question-sign" href="#add_email_collabs"></i>
        </tr>
        <tr><th colspan=2><em><strong>Outgoing Emails</strong>: Default email only applies to outgoing emails without SMTP setting.</em></th></tr>
        <tr><td width="180">Default Outgoing Email:</td>
            <td class="form-group form-inline">
                <select class="form-control" name="default_smtp_id">
                    <option value=0 selected="selected">None: Use PHP mail function</option>
                    <?php
                    $sql='SELECT email_id,email,name,smtp_host FROM '.EMAIL_TABLE.' WHERE smtp_active=1';

                    if(($res=db_query($sql)) && db_num_rows($res)) {
                        while (list($id,$email,$name,$host) = db_fetch_row($res)){
                            $email=$name?"$name &lt;$email&gt;":$email;
                            ?>
                            <option value="<?php echo $id; ?>"<?php echo ($config['default_smtp_id']==$id)?'selected="selected"':''; ?>><?php echo $email; ?></option>
                        <?php
                        }
                    } ?>
                 </select>
                 <?php if($errors['default_smtp_id']) echo '<span class="alert alert-danger">' .$errors['default_smtp_id']. '</span>'; ?>
           </td>
       </tr>
    </tbody>
</table>
<p class="centered">
    <input class="btn btn-success" type="submit" name="submit" value="Save Changes">
    <input class="btn btn-warning" type="reset" name="reset" value="Reset Changes">
</p>
</form>
