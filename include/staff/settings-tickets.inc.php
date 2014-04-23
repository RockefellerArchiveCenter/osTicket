<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');
if(!($maxfileuploads=ini_get('max_file_uploads')))
    $maxfileuploads=DEFAULT_MAX_FILE_UPLOADS;
?>
<h2>Ticket Settings and Options</h2>
<form action="settings.php?t=tickets" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="tickets" >
<table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>Ticket Settings</h4>
                <em>Global ticket settings and options.</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr><td width="220" class="required">Ticket IDs:</td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="random_ticket_ids"  value="0" <?php echo !$config['random_ticket_ids']?'checked="checked"':''; ?> />
                <label> Sequential</label>
                <input class="form-control radio" type="radio" name="random_ticket_ids"  value="1" <?php echo $config['random_ticket_ids']?'checked="checked"':''; ?> />
                <label> Random  <em>(highly recommended)</em></label>
            </td>
        </tr>

        <tr>
            <td width="180" class="required">
                Default SLA:
            </td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="default_sla_id">
                    <option value="0">&mdash; None &mdash;</option>
                    <?php
                    if($slas=SLA::getSLAs()) {
                        foreach($slas as $id => $name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id,
                                    ($config['default_sla_id'] && $id==$config['default_sla_id'])?'selected="selected"':'',
                                    $name);
                        }
                    }
                    ?>
                </select>
                <?php if($errors['default_sla_id']) echo '<span class="alert alert-danger">' .$errors['default_sla_id']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">Default Priority:</td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="default_priority_id">
                    <?php
                    $priorities= db_query('SELECT priority_id,priority_desc FROM '.TICKET_PRIORITY_TABLE);
                    while (list($id,$tag) = db_fetch_row($priorities)){ ?>
                        <option value="<?php echo $id; ?>"<?php echo ($config['default_priority_id']==$id)?'selected':''; ?>><?php echo $tag; ?></option>
                    <?php
                    } ?>
                </select>
                <?php if($errors['default_priority_id']) echo '<span class="alert alert-danger">' . $errors['default_priority_id']. '</span>'; ?>
             </td>
        </tr>
        <tr>
            <td>Maximum <b>Open</b> Tickets:</td>
            <td class="form-group form-inline">
                <input class="form-control" type="text" name="max_open_tickets" size=4 value="<?php echo $config['max_open_tickets']; ?>">
                <label> per email/user. <em>(Helps with spam and email flood control - enter 0 for unlimited)</em><label>
            </td>
        </tr>
        <tr>
            <td>Ticket Auto-lock Time:</td>
            <td class="form-group form-inline">
                <input class="form-control" type="text" name="autolock_minutes" size=4 value="<?php echo $config['autolock_minutes']; ?>">
                <?php if($errors['autolock_minites']) echo '<span class="alert alert-danger">' .$errors['autolock_minutes']. '</span>'; ?>
                <label>(Minutes to lock a ticket on activity - enter 0 to disable locking)</label>
            </td>
        </tr>
        <tr>
            <td width="180">Show Related Tickets:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="show_related_tickets" value="1" <?php echo $config['show_related_tickets'] ?'checked="checked"':''; ?> >
                <label>(Show all related tickets on user login - otherwise access is restricted to one ticket view per login)</label>
            </td>
        </tr>
        <tr>
            <td>Human Verification:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="enable_captcha" <?php echo $config['enable_captcha']?'checked="checked"':''; ?>>
                <label> Enable CAPTCHA on new web tickets.<em>(requires GDLib)</em></label>
                <?php if($errors['enable_captcha']) echo '<span class="alert alert-danger">' .$errors['enable_captcha'].'</span>'; ?>
            </td>
        </tr>
        <tr>
            <td>Claim Tickets:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="auto_claim_tickets" <?php echo $config['auto_claim_tickets']?'checked="checked"':''; ?>>
                <label> Auto-assign unassigned tickets on response</label>
                <!-- Help Tip:
                     Reopened tickets are always assigned to the last respondent -->
            </td>
        </tr>
        <tr>
            <td>Assigned Tickets:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="show_assigned_tickets" <?php echo $config['show_assigned_tickets']?'checked="checked"':''; ?>>
                <label> Show assigned tickets on open queue.</label>
            </td>
        </tr>
        <tr>
            <td>Answered Tickets:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="show_answered_tickets" <?php echo $config['show_answered_tickets']?'checked="checked"':''; ?>>
                <label> Show answered tickets on open queue.</label>
            </td>
        </tr>
        <tr>
            <td>Staff Identity Masking:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="hide_staff_name" <?php echo $config['hide_staff_name']?'checked="checked"':''; ?>>
                <label> Hide staff's name on responses. </label>
            </td>
        </tr>
        <tr>
            <td>Enable HTML Ticket Thread:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="enable_html_thread" <?php
                echo $config['enable_html_thread']?'checked="checked"':''; ?>>
                <label> Enable rich text in ticket thread and autoresponse emails</label>
            </td>
        </tr>
        <tr>
            <td>Allow Client Updates:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="allow_client_updates" <?php
                echo $config['allow_client_updates']?'checked="checked"':''; ?>>
                <label> Allow clients to update ticket details via the web portal</label>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><b>Attachments</b>:  Size and max. uploads setting mainly apply to web tickets.</em>
            </th>
        </tr>
        <tr>
            <td width="180">Allow Attachments:</td>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="allow_attachments" <?php echo $config['allow_attachments']?'checked="checked"':''; ?>><b>Allow Attachments</b>
                &nbsp; <em>(Global Setting)</em>
                <?php if($errors['allow_attachments']) echo '<span class="alert alert-danger">' .$errors['allow_attachments']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">Emailed/API Attachments:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="allow_email_attachments" <?php echo $config['allow_email_attachments']?'checked="checked"':''; ?>> Accept emailed/API attachments.
                <?php if($errors['allow_email_attachments']) echo '<span class="alert alert-danger">' .$errors['allow_email_attachments']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">Online/Web Attachments:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="allow_online_attachments" <?php echo $config['allow_online_attachments']?'checked="checked"':''; ?> >
                    <label> Allow web upload</label>
                <input  class="form-control checkbox" type="checkbox" name="allow_online_attachments_onlogin" <?php echo $config['allow_online_attachments_onlogin'] ?'checked="checked"':''; ?> >
                   <label> Limit to authenticated users only. <em>(User must be logged in to upload files)</em></label>
                   <?php if($errors['allow_online_attachments']) echo '<span class="alert alert-danger">' .$errors['allow_online_attachments']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td>Max. User File Uploads:</td>
            <td class="form-group form-inline">
                <select class="form-control" name="max_user_file_uploads">
                    <?php
                    for($i = 1; $i <=$maxfileuploads; $i++) {
                        ?>
                        <option <?php echo $config['max_user_file_uploads']==$i?'selected="selected"':''; ?> value="<?php echo $i; ?>">
                            <?php echo $i; ?>&nbsp;<?php echo ($i>1)?'files':'file'; ?></option>
                        <?php
                    } ?>
                </select>
                <label>(Number of files the user is allowed to upload simultaneously)</label>
                <?php if($errors['max_user_file_uploads']) echo '<span class="alert alert-danger">' .$errors['max_user_file_uploads']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td>Max. Staff File Uploads:</td>
            <td class="form-group form-inline">
                <select class="form-control" name="max_staff_file_uploads">
                    <?php
                    for($i = 1; $i <=$maxfileuploads; $i++) {
                        ?>
                        <option <?php echo $config['max_staff_file_uploads']==$i?'selected="selected"':''; ?> value="<?php echo $i; ?>">
                            <?php echo $i; ?>&nbsp;<?php echo ($i>1)?'files':'file'; ?></option>
                        <?php
                    } ?>
                </select>
                <label>(Number of files the staff is allowed to upload simultaneously)</label>
                <?php if($errors['landing_page_id']) echo '<span class="alert alert-danger">' .$errors['max_staff_file_uploads'].'</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">Maximum File Size:</td>
            <td class="form-group form-inline">
                <select class="form-control" name="max_file_size">
                    <option value="262144">&mdash; Small &mdash;</option>
                    <?php $next = 512 << 10;
                    $max = strtoupper(ini_get('upload_max_filesize'));
                    $limit = (int) $max;
                    if (!$limit) $limit = 2 << 20; # 2M default value
                    elseif (strpos($max, 'K')) $limit <<= 10;
                    elseif (strpos($max, 'M')) $limit <<= 20;
                    elseif (strpos($max, 'G')) $limit <<= 30;
                    while ($next <= $limit) {
                        // Select the closest, larger value (in case the
                        // current value is between two)
                        $diff = $next - $config['max_file_size'];
                        $selected = ($diff >= 0 && $diff < $next / 2)
                            ? 'selected="selected"' : ''; ?>
                        <option value="<?php echo $next; ?>" <?php echo $selected;
                             ?>><?php echo Format::file_size($next);
                             ?></option><?php
                        $next *= 2;
                    }
                    // Add extra option if top-limit in php.ini doesn't fall
                    // at a power of two
                    if ($next < $limit * 2) {
                        $selected = ($limit == $config['max_file_size'])
                            ? 'selected="selected"' : ''; ?>
                        <option value="<?php echo $limit; ?>" <?php echo $selected;
                             ?>><?php echo Format::file_size($limit);
                             ?></option><?php
                    }
                    ?>
                </select>
                <?php if($errors['max_file_size']) echo '<span class="alert alert-danger">' .$errors['max_file_size']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">Ticket Response Files:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="email_attachments" <?php echo $config['email_attachments']?'checked="checked"':''; ?> >
                <label> Email attachments to the user</label>
            </td>
        </tr>
        <?php if (($bks = FileStorageBackend::allRegistered())
                && count($bks) > 1) { ?>
        <tr>
            <td width="180">Store Attachments:</td>
            <td class="form-group form-inline">
            <select class="form-control" name="default_storage_bk"><?php
                foreach ($bks as $char=>$class) {
                    $selected = $config['default_storage_bk'] == $char
                        ? 'selected="selected"' : '';
                    ?><option <?php echo $selected; ?> value="<?php echo $char; ?>"
                    ><?php echo $class::$desc; ?></option><?php
                } ?>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <th colspan="2">
                <em><strong>Accepted File Types</strong>: Limit the type of files users are allowed to submit.</em>
                <?php if($errors['allowed_filetypes']) echo '<span class="alert alert-danger">' .$errors['allowed_filetypes']. '</span>'; ?>
            </th>
        </tr>
        <tr>
            <td class="form-group form-inline" colspan="2">
                <p class="help-block">Enter allowed file extensions separated by a comma. e.g .doc, .pdf. To accept all files enter wildcard <b><i>.*</i></b>&nbsp;i.e dotStar (NOT Recommended).</p>
                <textarea class="form-control" name="allowed_filetypes" cols="21" rows="4" style="width: 65%;" wrap="hard" ><?php echo $config['allowed_filetypes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p class="centered">
    <input class="btn btn-success" type="submit" name="submit" value="Save Changes">
    <input class="btn btn-warning" type="reset" name="reset" value="Reset Changes">
</p>
</form>

