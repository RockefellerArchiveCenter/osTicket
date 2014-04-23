<h2>Alerts and Notices</h2>
<form action="settings.php?t=alerts" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="alerts" >
<table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th>
                <h4>Alerts and Notices sent to staff on ticket "events"</h4>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr><th><em><b>New Ticket Alert</b>:
            <i class="help-tip icon-question-sign" href="#new_ticket"></i>
            </em></th></tr>
        <tr>
            <td class="form-group form-inline">
            <em><b>Status:</b></em>
                <input class="form-control radio" type="radio" name="ticket_alert_active"  value="1"   <?php echo $config['ticket_alert_active']?'checked':''; ?> />
                <label> Enable</label>
                <input class="form-control radio" type="radio" name="ticket_alert_active"  value="0"   <?php echo !$config['ticket_alert_active']?'checked':''; ?> />
                <label> Disable</label>
                <?php if($errors['ticket_alert_active']) echo '<span class="alert alert-danger">' .$errors['ticket_alert_active']. '</span>'; ?>
             </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="ticket_alert_admin" <?php echo $config['ticket_alert_admin']?'checked':''; ?>>
                <label> Admin Email (<?php echo $cfg->getAdminEmail(); ?>)</label>
            </td>
        </tr>
        <tr>    
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="ticket_alert_dept_manager" <?php echo $config['ticket_alert_dept_manager']?'checked':''; ?>>
                <label> Department Manager</label>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="ticket_alert_dept_members" <?php echo $config['ticket_alert_dept_members']?'checked':''; ?>>
                <label> Department Members (spammy)<label>
            </td>
        </tr>
        <tr><th><em><b>New Message Alert</b>:
            <i class="help-tip icon-question-sign" href="#new_message"></i>
            </em></th></tr>
        <tr>
            <td class="form-group form-inline">
            <em><b>Status:</b></em>
              <input class="form-control radio" type="radio" name="message_alert_active"  value="1"   <?php echo $config['message_alert_active']?'checked':''; ?> />
              <label> Enable</label>
              <input class="form-control radio" type="radio" name="message_alert_active"  value="0"   <?php echo !$config['message_alert_active']?'checked':''; ?> />
              <label> Disable</label>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="message_alert_laststaff" <?php echo $config['message_alert_laststaff']?'checked':''; ?>>
              <label> Last Respondent</label>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="message_alert_assigned" <?php echo $config['message_alert_assigned']?'checked':''; ?>>
              <label> Assigned Staff</label>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="message_alert_dept_manager" <?php echo $config['message_alert_dept_manager']?'checked':''; ?>>
              <label> Department Manager (spammy)<label>
            </td>
        </tr>
        <tr><th><em><b>New Internal Note Alert</b>:
            <i class="help-tip icon-question-sign" href="#new_activity"></i>
            </em></th></tr>
        <tr>
            <td class="form-group form-inline">
            <em><b>Status:</b></em>
              <input class="form-control radio" type="radio" name="note_alert_active"  value="1"   <?php echo $config['note_alert_active']?'checked':''; ?> />
              <label> Enable</label>
              <input class="form-control radio" type="radio" name="note_alert_active"  value="0"   <?php echo !$config['note_alert_active']?'checked':''; ?> />
              <label> Disable</label>
              <?php if($errors['note_alert_active']) echo '<span class="alert alert-danger">' .$errors['note_alert_active']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="note_alert_laststaff" <?php echo $config['note_alert_laststaff']?'checked':''; ?>>
              <label> Last Respondent</label>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="note_alert_assigned" <?php echo $config['note_alert_assigned']?'checked':''; ?>>
              <label> Assigned Staff</label>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="note_alert_dept_manager" <?php echo $config['note_alert_dept_manager']?'checked':''; ?>>
              <label> Department Manager (spammy)</label>
            </td>
        </tr>
        <tr>
        <th>
        <em><b>Ticket Assignment Alert</b>:
            <i class="help-tip icon-question-sign" href="#assign_alert"></i>
            </em></th></tr>
        <tr>
            <td class="form-group form-inline">
            <em><b>Status: </b></em> &nbsp;
              <input class="form-control radio" name="assigned_alert_active" value="1" checked="checked" type="radio">
              <label> Enable</label>
              <input class="form-control radio" name="assigned_alert_active" value="0" type="radio">
              <label> Disable</label>
               <?php if ($errors['assigned_alert_active']) echo '<span class="alert alert-danger">' .$errors['assigned_alert_active']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="assigned_alert_staff" <?php echo $config['assigned_alert_staff']?'checked':''; ?>>
              <label> Assigned Staff</label>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox"name="assigned_alert_team_lead" <?php echo $config['assigned_alert_team_lead']?'checked':''; ?>>
              <label> Team Lead (On team assignment)</label>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="assigned_alert_team_members" <?php echo $config['assigned_alert_team_members']?'checked':''; ?>>
              <label> Team Members (spammy)</label>
            </td>
        </tr>
        <tr><th><em><b>Ticket Transfer Alert</b>:
            <i class="help-tip icon-question-sign" href="#transfer_alert"></i>
            </em></th></tr>
        <tr>
            <td class="form-group form-inline">
            <em><b>Status:</b></em>
              <input class="form-control radio" type="radio" name="transfer_alert_active"  value="1"   <?php echo $config['transfer_alert_active']?'checked':''; ?> />
              <label> Enable</label>
              <input class="form-control radio" type="radio" name="transfer_alert_active"  value="0"   <?php echo !$config['transfer_alert_active']?'checked':''; ?> />
              <label> Disable</label>
              <?php if ($errors['alert_alert_active']) echo '<span class="alert alert-danger">' .$errors['alert_alert_active']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="transfer_alert_assigned" <?php echo $config['transfer_alert_assigned']?'checked':''; ?>>
              <label> Assigned Staff/Team</label>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="transfer_alert_dept_manager" <?php echo $config['transfer_alert_dept_manager']?'checked':''; ?>>
              <label> Department Manager</label>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="transfer_alert_dept_members" <?php echo $config['transfer_alert_dept_members']?'checked':''; ?>>
              <label> Department Members (spammy)</label>
            </td>
        </tr>
        <tr><th><em><b>Overdue Ticket Alert</b>:
            <i class="help-tip icon-question-sign" href="#stale_alert"></i>
            </em></th></tr>
        <tr>
            <td class="form-group form-inline">
            <em><b>Status:</b></em>
              <input class="form-control radio" type="radio" name="overdue_alert_active"  value="1"   <?php echo $config['overdue_alert_active']?'checked':''; ?> />
              <label> Enable</label>
              <input class="form-control radio" type="radio" name="overdue_alert_active"  value="0"   <?php echo !$config['overdue_alert_active']?'checked':''; ?> />
              <label> Disable</label>
              <?php if($errors['overdue_alert_active']) echo '<span class="alert alert-danger">' .$errors['overdue_alert_active']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="overdue_alert_assigned" <?php echo $config['overdue_alert_assigned']?'checked':''; ?>>
              <label> Assigned Staff/Team</label>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="overdue_alert_dept_manager" <?php echo $config['overdue_alert_dept_manager']?'checked':''; ?>>
              <label> Department Manager</label>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="overdue_alert_dept_members" <?php echo $config['overdue_alert_dept_members']?'checked':''; ?>>
              <label> Department Members (spammy)</label>
            </td>
        </tr>
        <tr><th><em><b>System Alerts</b>:
            <i class="help-tip icon-question-sign" href="#meltdowns"></i>
            </em></th></tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="send_sys_errors" checked="checked" disabled="disabled">
              <label> System Errors</label>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="send_sql_errors" <?php echo $config['send_sql_errors']?'checked':''; ?>>
              <label> SQL errors</label>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
              <input class="form-control checkbox" type="checkbox" name="send_login_errors" <?php echo $config['send_login_errors']?'checked':''; ?>>
              <label> Excessive Login attempts</label>
            </td>
        </tr>
    </tbody>
</table>
<p class="centered">
    <input class="btn btn-success" type="submit" name="submit" value="Save Changes">
    <input class="btn btn-warning" type="reset" name="reset" value="Reset Changes">
</p>
</form>
