<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
$info=array();
$qstr='';
if($group && $_REQUEST['a']!='add'){
    $title='Update Group';
    $action='update';
    $submit_text='Save Changes';
    $info=$group->getInfo();
    $info['id']=$group->getId();
    $info['depts']=$group->getDepartments();
    $qstr.='&id='.$group->getId();
}else {
    $title='Add New Group';
    $action='create';
    $submit_text='Create Group';
    $info['isactive']=isset($info['isactive'])?$info['isactive']:1;
    $info['can_create_tickets']=isset($info['can_create_tickets'])?$info['can_create_tickets']:1;
    $qstr.='&a='.$_REQUEST['a'];
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="groups.php?<?php echo $qstr; ?>" method="post" id="save" name="group">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2>User Group</h2>
 <table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><strong>Group Information</strong>: Disabled group will limit staff members access. Admins are exempted.</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="180" class="required">
                Name:
            </td>
            <td class="form-group form-inline has-error">
                <input class="form-control" type="text" size="30" name="name" value="<?php echo $info['name']; ?>">
                <?php if($errors['name']) echo '<span class="alert alert-danger">' .$errors['name']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                Status:
            </td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="isactive" value="1" <?php echo $info['isactive']?'checked="checked"':''; ?>><label>Active</label>
                <input class="form-control radio" type="radio" name="isactive" value="0" <?php echo !$info['isactive']?'checked="checked"':''; ?>><label>Disabled</label>
                <?php if($errors['status']) echo '<span class="alert alert-danger">' .$errors['status']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Group Permissions</strong>: Applies to all group members&nbsp;</em>
            </th>
        </tr>
        <tr><td>Can <b>Create</b> Tickets</td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="can_create_tickets"  value="1"   <?php echo $info['can_create_tickets']?'checked="checked"':''; ?> />
                <label>Yes</label>
                <input class="form-control radio" type="radio" name="can_create_tickets"  value="0"   <?php echo !$info['can_create_tickets']?'checked="checked"':''; ?> />
                <label>No</label>
                <p class="help-block">Ability to open tickets on behalf of clients.</p>
            </td>
        </tr>
        <tr><td>Can <b>Edit</b> Tickets</td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="can_edit_tickets"  value="1"   <?php echo $info['can_edit_tickets']?'checked="checked"':''; ?> />
                <label>Yes</label>
                <input class="form-control radio" type="radio" name="can_edit_tickets"  value="0"   <?php echo !$info['can_edit_tickets']?'checked="checked"':''; ?> />
                <label>No</label>
                <p class="help-block">Ability to edit tickets.</p>
            </td>
        </tr>
        <tr><td>Can <b>Post Reply</b></td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="can_post_ticket_reply"  value="1"   <?php echo $info['can_post_ticket_reply']?'checked="checked"':''; ?> />
                <label>Yes</label>
                <input class="form-control radio" type="radio" name="can_post_ticket_reply"  value="0"   <?php echo !$info['can_post_ticket_reply']?'checked="checked"':''; ?> />
                <label>No</label>
                <p class="help-block">Ability to post a ticket reply.</p>
            </td>
        </tr>
        <tr><td>Can <b>Close</b> Tickets</td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="can_close_tickets"  value="1" <?php echo $info['can_close_tickets']?'checked="checked"':''; ?> />
                <label>Yes</label>
                <input class="form-control radio" type="radio" name="can_close_tickets"  value="0" <?php echo !$info['can_close_tickets']?'checked="checked"':''; ?> />
                <label>No</label>
                <p class="help-block">Ability to close tickets. Staff can still post a response.</p>
            </td>
        </tr>
        <tr><td>Can <b>Assign</b> Tickets</td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="can_assign_tickets"  value="1" <?php echo $info['can_assign_tickets']?'checked="checked"':''; ?> />
                <label>Yes</label>
                <input class="form-control radio" type="radio" name="can_assign_tickets"  value="0" <?php echo !$info['can_assign_tickets']?'checked="checked"':''; ?> />
                <label>No</label>
                <p class="help-block">Ability to assign tickets to staff members.</p>
            </td>
        </tr>
        <tr><td>Can <b>Transfer</b> Tickets</td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="can_transfer_tickets"  value="1" <?php echo $info['can_transfer_tickets']?'checked="checked"':''; ?> />
                <label>Yes</label>
                <input class="form-control radio" type="radio" name="can_transfer_tickets"  value="0" <?php echo !$info['can_transfer_tickets']?'checked="checked"':''; ?> />
                <label>No</label>
                <p class="help-block">Ability to transfer tickets between departments.</p>
            </td>
        </tr>
        <tr><td>Can <b>Delete</b> Tickets</td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="can_delete_tickets"  value="1"   <?php echo $info['can_delete_tickets']?'checked="checked"':''; ?> />
                <label>Yes</label>
                <input class="form-control radio" type="radio" name="can_delete_tickets"  value="0"   <?php echo !$info['can_delete_tickets']?'checked="checked"':''; ?> />
                <label>No</label>
                <p class="help-block">Ability to delete tickets (Deleted tickets can't be recovered!)</p>
            </td>
        </tr>
        <tr><td>Can Ban Emails</td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="can_ban_emails"  value="1" <?php echo $info['can_ban_emails']?'checked="checked"':''; ?> />
                <label>Yes</label>
                <input class="form-control radio" type="radio" name="can_ban_emails"  value="0" <?php echo !$info['can_ban_emails']?'checked="checked"':''; ?> />
                <label>No</label>
                <p class="help-block">Ability to add/remove emails from banlist via ticket interface.</p>
            </td>
        </tr>
        <tr><td>Can Manage Premade</td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="can_manage_premade"  value="1" <?php echo $info['can_manage_premade']?'checked="checked"':''; ?> />
                <label>Yes</label>
                <input class="form-control radio" type="radio" name="can_manage_premade"  value="0" <?php echo !$info['can_manage_premade']?'checked="checked"':''; ?> />
                <label>No</label>
                <p class="help-block">Ability to add/update/disable/delete canned responses and attachments.</p>
            </td>
        </tr>
        <tr><td>Can Manage FAQ</td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="can_manage_faq"  value="1" <?php echo $info['can_manage_faq']?'checked="checked"':''; ?> />
                <label>Yes</label>
                <input class="form-control radio" type="radio" name="can_manage_faq"  value="0" <?php echo !$info['can_manage_faq']?'checked="checked"':''; ?> />
                <label>No</label>
                <p class="help-block">Ability to add/update/disable/delete knowledgebase categories and FAQs.</p>
            </td>
        </tr>
        <tr><td>Can View Staff Stats.</td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="can_view_staff_stats"  value="1" <?php echo $info['can_view_staff_stats']?'checked="checked"':''; ?> />
                <label>Yes</label>
                <input class="form-control radio" type="radio" name="can_view_staff_stats"  value="0" <?php echo !$info['can_view_staff_stats']?'checked="checked"':''; ?> />
                <label>No</label>
                <p class="help-block">Ability to view stats of other staff members in allowed departments.</p>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Department Access</strong>: Check all departments the group members are allowed to access.&nbsp;&nbsp;&nbsp;<a id="selectAll" href="#deptckb">Select All</a>&nbsp;&nbsp;<a id="selectNone" href="#deptckb">Select None</a>&nbsp;&nbsp;</em>
            </th>
        </tr>
        <?php
         $sql='SELECT dept_id,dept_name FROM '.DEPT_TABLE.' ORDER BY dept_name';
         if(($res=db_query($sql)) && db_num_rows($res)){
            while(list($id,$name) = db_fetch_row($res)){
                $ck=($info['depts'] && in_array($id,$info['depts']))?'checked="checked"':'';
                echo sprintf('<tr><td class="form-group form-inline" colspan="2"><input class="form-control checkbox" type="checkbox" class="deptckb" name="depts[]" value="%d" %s>&nbsp;<label>%s</label></td></tr>',$id,$ck,$name);
            }
         }
        ?>
        <tr>
            <th colspan="2">
                <em><strong>Admin Notes</strong>: Internal notes viewable by all admins.&nbsp;</em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea class="richtext no-bar" name="notes" cols="21"
                    rows="8" style="width: 80%;"><?php echo $info['notes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p class="centered">
    <input class="btn btn-success" type="submit" name="submit" value="<?php echo $submit_text; ?>">
    <input class="btn btn-warning" type="reset"  name="reset"  value="Reset">
    <input class="btn btn-danger" type="button" name="cancel" value="Cancel" onclick='window.location.href="groups.php"'>
</p>
</form>
