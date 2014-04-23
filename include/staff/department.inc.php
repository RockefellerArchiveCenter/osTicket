<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
$info=array();
$qstr='';
if($dept && $_REQUEST['a']!='add') {
    //Editing Department.
    $title='Update Department';
    $action='update';
    $submit_text='Save Changes';
    $info=$dept->getInfo();
    $info['id']=$dept->getId();
    $info['groups'] = $dept->getAllowedGroups();

    $qstr.='&id='.$dept->getId();
} else {
    $title='Add New Department';
    $action='create';
    $submit_text='Create Dept';
    $info['ispublic']=isset($info['ispublic'])?$info['ispublic']:1;
    $info['ticket_auto_response']=isset($info['ticket_auto_response'])?$info['ticket_auto_response']:1;
    $info['message_auto_response']=isset($info['message_auto_response'])?$info['message_auto_response']:1;
    $qstr.='&a='.$_REQUEST['a'];
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="departments.php?<?php echo $qstr; ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2>Department</h2>
 <table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em>Department Information</em>
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
                <?php if ($errors['name']) echo '<span class="alert alert-danger">' .$errors['name']. '</span>'; ?></span>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                Type:
            </td>
            <td class="form-group form-inline has-error">
                <input type="radio" name="ispublic" value="1" <?php echo $info['ispublic']?'checked="checked"':''; ?>><label>Public</label>
                <input type="radio" name="ispublic" value="0" <?php echo !$info['ispublic']?'checked="checked"':''; ?>><label>Private (Internal)</label>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                Email:
            </td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="email_id">
                    <option value="0">&mdash; Select Department Email &mdash;</option>
                    <?php
                    $sql='SELECT email_id,email,name FROM '.EMAIL_TABLE.' email ORDER by name';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while(list($id,$email,$name)=db_fetch_row($res)){
                            $selected=($info['email_id'] && $id==$info['email_id'])?'selected="selected"':'';
                            if($name)
                                $email=Format::htmlchars("$name <$email>");
                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$email);
                        }
                    }
                    ?>
                </select>
                <?php if ($errors['email_id']) echo '<span class="alert alert-danger">' .$errors['email_id']. '</span>'; ?></span>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                Template:
            </td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="tpl_id">
                    <option value="0">&mdash; System Default &mdash;</option>
                    <?php
                    $sql='SELECT tpl_id,name FROM '.EMAIL_TEMPLATE_GRP_TABLE.' tpl WHERE isactive=1 ORDER by name';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while(list($id,$name)=db_fetch_row($res)){
                            $selected=($info['tpl_id'] && $id==$info['tpl_id'])?'selected="selected"':'';
                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$name);
                        }
                    }
                    ?>
                </select>
                <?php if ($errors['tpl_id']) echo '<span class="alert alert-danger">' .$errors['tpl_id']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                SLA:
            </td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="sla_id">
                    <option value="0">&mdash; System Default &mdash;</option>
                    <?php
                    if($slas=SLA::getSLAs()) {
                        foreach($slas as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['sla_id']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>
                <?php if ($errors['sla_id']) echo '<span class="alert alert-danger">' .$errors['sla_id']. '</span>'; ?></span>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                Manager:
            </td>
            <td class="form-group form-inline">
                <select class="form-control" name="manager_id">
                    <option value="0">&mdash; None &mdash;</option>
                    <?php
                    $sql='SELECT staff_id,CONCAT_WS(", ",lastname, firstname) as name '
                        .' FROM '.STAFF_TABLE.' staff '
                        .' ORDER by name';
                    if(($res=db_query($sql)) && db_num_rows($res)) {
                        while(list($id,$name)=db_fetch_row($res)){
                            $selected=($info['manager_id'] && $id==$info['manager_id'])?'selected="selected"':'';
                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$name);
                        }
                    }
                    ?>
                </select>
                <?php if ($errors['manager_id']) echo '<span class="alert alert-danger">' .$errors['manager_id']. '</span>'; ?></span>
            </td>
        </tr>
        <tr>
            <td width="180">
                Group Membership:
            </td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="group_membership" value="0" <?php echo $info['group_membership']?'checked="checked"':''; ?> >
                <label>Extend membership to groups with access. <i>(Alerts and  notices will include groups)</i></label>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Auto Response Settings</strong>: Override global auto-response settings for tickets routed to the Dept.</em>
            </th>
        </tr>
        <tr>
            <td width="180">
                New Ticket:
            </td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="ticket_auto_response" value="0" <?php echo !$info['ticket_auto_response']?'checked="checked"':''; ?> >
                <label>Disable new ticket auto-response for this Dept.</label>
            </td>
        </tr>
        <tr>
            <td width="180">
                New Message:
            </td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="message_auto_response" value="0" <?php echo !$info['message_auto_response']?'checked="checked"':''; ?> >
                    <label>Disable new message auto-response for this Dept.</label>
            </td>
        </tr>
        <tr>
            <td width="180">
                Auto-Response Email:
            </td>
            <td class="form-group form-inline">
                <select class="form-control" name="autoresp_email_id">
                    <option value="0" selected="selected">&mdash; Department Email &mdash;</option>
                    <?php
                    $sql='SELECT email_id,email,name FROM '.EMAIL_TABLE.' email ORDER by name';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while(list($id,$email,$name)=db_fetch_row($res)){
                            $selected = (isset($info['autoresp_email_id'])
                                    && $id == $info['autoresp_email_id'])
                                ? 'selected="selected"' : '';
                            if($name)
                                $email=Format::htmlchars("$name <$email>");
                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$email);
                        }
                    }
                    ?>
                </select>
                <?php if ($errors['autoresp_email_id']) echo '<span class="alert alert-danger">' .$errors['autoresp_email_id']. '</span>'; ?></span>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Department Access</strong>: Check all groups allowed to access this department.</em>
            </th>
        </tr>
        <tr><td colspan=2><em>Department manager and primary members will always have access independent of group selection or assignment.</em></td></tr>
        <?php
         $sql='SELECT group_id, group_name, count(staff.staff_id) as members '
             .' FROM '.GROUP_TABLE.' grp '
             .' LEFT JOIN '.STAFF_TABLE. ' staff USING(group_id) '
             .' GROUP by grp.group_id '
             .' ORDER BY group_name';
         if(($res=db_query($sql)) && db_num_rows($res)){
            while(list($id, $name, $members) = db_fetch_row($res)) {
                if($members>0)
                    $members=sprintf('<a href="staff.php?a=filter&gid=%d">%d</a>', $id, $members);

                $ck=($info['groups'] && in_array($id,$info['groups']))?'checked="checked"':'';
                echo sprintf('<tr><td colspan=2>&nbsp;&nbsp;<label><input type="checkbox" name="groups[]" value="%d" %s>&nbsp;%s</label> (%s)</td></tr>',
                        $id, $ck, $name, $members);
            }
         }
        ?>
        <tr>
            <th colspan="2">
                <em><strong>Department Signature</strong>: Optional signature used on outgoing emails. <?php if ($errors['signature']) echo '<span class="alert alert-danger">' .$errors['signature']. '</span>'; ?></span></em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea class="richtext no-bar form-control" name="signature" cols="21"
                    rows="5" style="width: 60%;"><?php echo $info['signature']; ?></textarea>
                <br><em>Signature is made available as a choice, for public departments, on ticket reply.</em>
            </td>
        </tr>
    </tbody>
</table>
<p class="centered">
    <input class="btn btn-success" type="submit" name="submit" value="<?php echo $submit_text; ?>">
    <input class="btn btn-warning" type="reset"  name="reset"  value="Reset">
    <input class="btn btn-danger" type="button" name="cancel" value="Cancel" onclick='window.location.href="departments.php"'>
</p>
</form>
