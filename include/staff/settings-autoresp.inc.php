<h2>Autoresponder Settings</h2>
<form action="settings.php?t=autoresp" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="autoresp" >
<table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>Autoresponder Setting</h4>
                <em>Global setting - can be disabled at department or email level.</em>
            </th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <td width="160">New Ticket:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="ticket_autoresponder" <?php
echo $config['ticket_autoresponder'] ? 'checked="checked"' : ''; ?>/>
                <label>Ticket Owner</label>
                <i class="help-tip icon-question-sign" href="#new_ticket"></i>
            </td>
        </tr>
        <tr>
            <td width="160">New Ticket by Staff:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="ticket_notice_active" <?php
echo $config['ticket_notice_active'] ? 'checked="checked"' : ''; ?>/>
                <label>Ticket Owner</label>
                <i class="help-tip icon-question-sign" href="#new_staff_ticket"></i>
            </td>
        </tr>
        <tr>
            <td width="160" rowspan="2">New Message:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="message_autoresponder" <?php
echo $config['message_autoresponder'] ? 'checked="checked"' : ''; ?>/>
                <label>Submitter: Send receipt confirmation</label>
                <i class="help-tip icon-question-sign" href="#new_message"></i>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="message_autoresponder_collabs" <?php
echo $config['message_autoresponder_collabs'] ? 'checked="checked"' : ''; ?>/>
                <label>Participants: Send new activity notice</label>
                <i class="help-tip icon-question-sign" href="#collaborators"></i>
                </div>
            </td>
        </tr>
        <tr>
            <td width="160">Overlimit Notice:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="overlimit_notice_active" <?php
echo $config['overlimit_notice_active'] ? 'checked="checked"' : ''; ?>/>
                <label>Ticket Submitter</label>
                <i class="help-tip icon-question-sign" href="#overlimit_notice"></i>
            </td>
        </tr>
    </tbody>
</table>
<p class="centered">
    <input class="btn btn-success" type="submit" name="submit" value="Save Changes">
    <input class="btn btn-warning" type="reset" name="reset" value="Reset Changes">
</p>
</form>
