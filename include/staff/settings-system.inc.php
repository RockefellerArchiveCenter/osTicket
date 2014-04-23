<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');

$gmtime = Misc::gmtime();
?>
<h2>System Settings and Preferences - <span>osTicket (<?php echo $cfg->getVersion(); ?>)</span></h2>
<form action="settings.php?t=system" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="system" >
<table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>System Settings &amp; Preferences</h4>
                <em><b>General Settings</b>: Offline mode will disable client interface and only allow admins to login to Staff Control Panel</em>
            </th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <td width="220" class="required">Helpdesk Status:</td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="isonline"  value="1"   <?php echo $config['isonline']?'checked="checked"':''; ?> />
                <label>Online (Active)</label>
                <input class="form-control radio" type="radio" name="isonline"  value="0"   <?php echo !$config['isonline']?'checked="checked"':''; ?> />
                <label>Offline (Disabled)</label>
                <?php if($errors['isoffline']) echo '<span class="alert alert-danger">' .$config['isoffline']?'osTicket offline':''. '</span>'; ?>
                <i class="help-tip icon-question-sign" href="#helpdesk_status"></i>
            </td>
        </tr>
        <tr>
            <td width="220" class="required">Helpdesk URL:</td>
            <td class="form-group form-inline has-error">
                <input class="form-control" type="text" size="40" name="helpdesk_url" value="<?php echo $config['helpdesk_url']; ?>">
                <?php if($errors['helpdesk_url']) echo '<span class="alert alert-danger">' .$errors['helpdesk_url']. '</span>'; ?>
                <i class="help-tip icon-question-sign" href="#helpdesk_url"></i>
        </td>
        </tr>
        <tr>
            <td width="220" class="required">Helpdesk Name/Title:</td>
            <td class="form-group form-inline has-error">
            <input class="form-control" type="text" size="40" name="helpdesk_title" value="<?php echo $config['helpdesk_title']; ?>">
             <?php if($errors['helpdesk_title']) echo '<span class="alert alert-danger">' .$errors['helpdesk_title']. '</span>'; ?>
                <i class="help-tip icon-question-sign" href="#helpdesk_name"></i>
            </td>
        </tr>
        <tr>
            <td width="220" class="required">Default Department:</td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="default_dept_id">
                    <option value="">&mdash; Select Default Department &mdash;</option>
                    <?php
                    $sql='SELECT dept_id,dept_name FROM '.DEPT_TABLE.' WHERE ispublic=1';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while (list($id, $name) = db_fetch_row($res)){
                            $selected = ($config['default_dept_id']==$id)?'selected="selected"':''; ?>
                            <option value="<?php echo $id; ?>"<?php echo $selected; ?>><?php echo $name; ?> Dept</option>
                        <?php
                        }
                    } ?>
                </select>
                <?php if($errors['default_dept_id']) echo '<span class="alert alert-danger">' .$errors['default_dept_id'].'</span>'; ?>
                <i class="help-tip icon-question-sign" href="#default_dept"></i>
            </td>
        </tr>

        <tr><td>Default Page Size:</td>
            <td class="form-group form-inline">
                <select class="form-control" name="max_page_size">
                    <?php
                     $pagelimit=$config['max_page_size'];
                    for ($i = 5; $i <= 50; $i += 5) {
                        ?>
                        <option <?php echo $config['max_page_size']==$i?'selected="selected"':''; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php
                    } ?>
                </select>
                <i class="help-tip icon-question-sign" href="#page_size"></i>
            </td>
        </tr>
        <tr>
            <td>Default Log Level:</td>
            <td class="form-group form-inline">
                <select class="form-control" name="log_level">
                    <option value=0 <?php echo $config['log_level'] == 0 ? 'selected="selected"':''; ?>>None (Disable Logger)</option>
                    <option value=3 <?php echo $config['log_level'] == 3 ? 'selected="selected"':''; ?>> DEBUG</option>
                    <option value=2 <?php echo $config['log_level'] == 2 ? 'selected="selected"':''; ?>> WARN</option>
                    <option value=1 <?php echo $config['log_level'] == 1 ? 'selected="selected"':''; ?>> ERROR</option>
                </select>
                <?php if($errors['log_level']) echo '<span class="alert alert-danger">' .$errors['log_level'] .'</span>'; ?>
                <i class="help-tip icon-question-sign" href="#log_level"></i>
            </td>
        </tr>
        <tr>
            <td>Purge Logs:</td>
            <td class="form-group form-inline">
                <select class="form-control" name="log_graceperiod">
                    <option value=0 selected>Never Purge Logs</option>
                    <?php
                    for ($i = 1; $i <=12; $i++) {
                        ?>
                        <option <?php echo $config['log_graceperiod']==$i?'selected="selected"':''; ?> value="<?php echo $i; ?>">
                            After&nbsp;<?php echo $i; ?>&nbsp;<?php echo ($i>1)?'Months':'Month'; ?></option>
                        <?php
                    } ?>
                </select>
                <i class="help-tip icon-question-sign" href="#purge_logs"></i>
            </td>
        </tr>
        <tr>
            <td width="180">Default Name Formatting:</td>
            <td class="form-group form-inline">
                <select class="form-control" name="name_format">
<?php foreach (PersonsName::allFormats() as $n=>$f) {
    list($desc, $func) = $f;
    $selected = ($config['name_format'] == $n) ? 'selected="selected"' : ''; ?>
                    <option value="<?php echo $n; ?>" <?php echo $selected;
                        ?>><?php echo $desc; ?></option>
<?php } ?>
                </select>
                <i class="help-tip icon-question-sign" href="#name_format"></i>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><b>Authentication Settings</b></em>
            </th>
        </tr>
        <tr><td>Password Expiration Policy:</th>
            <td class="form-group form-inline">
                <select class="form-control" name="passwd_reset_period">
                   <option value="0"> &mdash; No expiration &mdash;</option>
                  <?php
                    for ($i = 1; $i <= 12; $i++) {
                        echo sprintf('<option value="%d" %s>%s%s</option>',
                                $i,(($config['passwd_reset_period']==$i)?'selected="selected"':''), $i>1?"Every $i ":'', $i>1?' Months':'Monthly');
                    }
                    ?>
                </select>
                <?php if($errors['passwd_reset_period']) echo '<span class="alert alert-danger">' .$errors['passwd_reset_period']. '</span>'; ?>
                <i class="help-tip icon-question-sign" href="#password_reset"></i>
            </td>
        </tr>
        <tr><td>Allow Password Resets:</th>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="allow_pw_reset" <?php echo $config['allow_pw_reset']?'checked="checked"':''; ?>>
              <label>Enables the Forgot my password link on the staff control panel</label>
            </td>
        </tr>
        <tr><td>Password Reset Window:</th>
            <td class="form-group form-inline">
              <input class="form-control" type="text" name="pw_reset_window" size="6" value="<?php
                    echo $config['pw_reset_window']; ?>">
                <label>Maximum time <em>in minutes</em> a password reset token can be valid.</label>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['pw_reset_window']; ?></font>
            </td>
        </tr>
        <tr><td>Staff Excessive Logins:</td>
            <td class="form-group form-inline">
                <select class="form-control" name="staff_max_logins">
                  <?php
                    for ($i = 1; $i <= 10; $i++) {
                        echo sprintf('<option value="%d" %s>%d</option>', $i,(($config['staff_max_logins']==$i)?'selected="selected"':''), $i);
                    }
                    ?>
                </select><label> failed login attempt(s) allowed before a </label>
                <select class="form-control" name="staff_login_timeout">
                  <?php
                    for ($i = 1; $i <= 10; $i++) {
                        echo sprintf('<option value="%d" %s>%d</option>', $i,(($config['staff_login_timeout']==$i)?'selected="selected"':''), $i);
                    }
                    ?>
                </select><label> minute lock-out is enforced.</label>
            </td>
        </tr>
        <tr><td>Staff Session Timeout:</td>
            <td class="form-group form-inline">
              <input class="form-control" type="text" name="staff_session_timeout" size=6 value="<?php echo $config['staff_session_timeout']; ?>">
              <label> Maximum idle time in minutes before a staff member must log in again (enter 0 to disable).</label>
            </td>
        </tr>
        <tr><td>Client Excessive Logins:</td>
            <td class="form-group form-inline">
                <select class="form-control" name="client_max_logins">
                  <?php
                    for ($i = 1; $i <= 10; $i++) {
                        echo sprintf('<option value="%d" %s>%d</option>', $i,(($config['client_max_logins']==$i)?'selected="selected"':''), $i);
                    }

                    ?>
                </select><label> failed login attempt(s) allowed before a </label>
                <select  class="form-control" name="client_login_timeout">
                  <?php
                    for ($i = 1; $i <= 10; $i++) {
                        echo sprintf('<option value="%d" %s>%d</option>', $i,(($config['client_login_timeout']==$i)?'selected="selected"':''), $i);
                    }
                    ?>
                </select><label> minute lock-out is enforced.</label>
            </td>
        </tr>

        <tr><td>Client Session Timeout:</td>
            <td class="form-group form-inline">
              <input class="form-control" type="text" name="client_session_timeout" size=6 value="<?php echo $config['client_session_timeout']; ?>">
              <label> Maximum idle time in minutes before a client must log in again (enter 0 to disable).</label>
            </td>
        </tr>
        <tr><td>Bind Staff Session to IP:</td>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="staff_ip_binding" <?php echo $config['staff_ip_binding']?'checked="checked"':''; ?>>
              <label>(binds staff session to originating IP address upon login)</label>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><b>Date and Time Options</b>&nbsp;
                <i class="help-tip icon-question-sign" href="#date_and_time"></i>
                </em>
            </th>
        </tr>
        <tr>
            <td width="220" class="required">Time Format:</td>
            <td class="form-group form-inline has-error">
                <input class="form-control" type="text" name="time_format" value="<?php echo $config['time_format']; ?>">
                    <?php if($errors['time_format']) echo '<span class="alert alert-danger">' .$errors['time_format'] . '</span>'; ?>
                    <label><?php echo Format::date($config['time_format'], $gmtime, $config['tz_offset'], $config['enable_daylight_saving']); ?></label>
             </td>
        </tr>
        <tr>
            <td width="220" class="required">Date Format:</td>
            <td class="form-group form-inline has-error">
            <input class="form-control" type="text" name="date_format" value="<?php echo $config['date_format']; ?>">
                        <?php if($errors['date_format']) echo '<span class="alert alert-danger">' .$errors['date_format']. '</span>'; ?>
                        <label><?php echo Format::date($config['date_format'], $gmtime, $config['tz_offset'], $config['enable_daylight_saving']); ?></label>
            </td>
        </tr>
        <tr>
            <td width="220" class="required">Date &amp; Time Format:</td>
            <td class="form-group form-inline has-error">
            <input class="form-control" type="text" name="datetime_format" value="<?php echo $config['datetime_format']; ?>">
                        <?php if($errors['datetime_format']) echo '<span class="alert alert-danger">' .$errors['datetime_format']. '</span>'; ?>
                        <label><?php echo Format::date($config['datetime_format'], $gmtime, $config['tz_offset'], $config['enable_daylight_saving']); ?></label>
            </td>
        </tr>
        <tr>
            <td width="220" class="required">Day, Date &amp; Time Format:</td>
            <td class="form-group form-inline has-error">
            <input class="form-control" type="text" name="daydatetime_format" value="<?php echo $config['daydatetime_format']; ?>">
                        <?php if($errors['daydatetime_format']) echo '<span class="alert alert-danger">' .$errors['daydatetime_format']. '</span>'; ?>
                        <label><?php echo Format::date($config['daydatetime_format'], $gmtime, $config['tz_offset'], $config['enable_daylight_saving']); ?></label>
            </td>
        </tr>
        <tr><td width="220" class="required">Default Time Zone:</td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="default_timezone_id">
                    <option value="">&mdash; Select Default Time Zone &mdash;</option>
                    <?php
                    $sql='SELECT id, offset,timezone FROM '.TIMEZONE_TABLE.' ORDER BY id';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while(list($id, $offset, $tz)=db_fetch_row($res)){
                            $sel=($config['default_timezone_id']==$id)?'selected="selected"':'';
                            echo sprintf('<option value="%d" %s>GMT %s - %s</option>', $id, $sel, $offset, $tz);
                        }
                    }
                    ?>
                </select>
                <?php if($errors['default_timezone_id']) echo '<span class="alert alert-danger">' .$errors['default_timezone_id'].'</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="220">Daylight Saving:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="enable_daylight_saving" <?php echo $config['enable_daylight_saving'] ? 'checked="checked"': ''; ?>>
                <label> Observe daylight savings</label>
            </td>
        </tr>
    </tbody>
</table>
<p class="centered">
    <input class="btn btn-success" type="submit" name="submit" value="Save Changes">
    <input class="btn btn-warning" type="reset" name="reset" value="Reset Changes">
</p>
</form>
