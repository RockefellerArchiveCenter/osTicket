<?php
if(!defined('OSTSCPINC') || !$thisstaff || !$thisstaff->canCreateTickets()) die('Access Denied');
$info=array();
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="tickets.php?a=open" method="post" id="save"  enctype="multipart/form-data">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="create">
 <input type="hidden" name="a" value="open">
 <h2>Open New Ticket</h2>
 <table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
    <!-- This looks empty - but beware, with fixed table layout, the user
         agent will usually only consult the cells in the first row to
         construct the column widths of the entire toable. Therefore, the
         first row needs to have two cells -->
        <tr><td></td><td></td></tr>
        <tr>
            <th colspan="2">
                <h4>New Ticket</h4>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="2">
                <em><strong>User Information</strong>: </em>
            </th>
        </tr>
        <?php
        if ($user) { ?>
        <tr><td>User:</td><td>
            <div id="user-info">
                <input type="hidden" name="uid" id="uid" value="<?php echo $user->getId(); ?>" />
            <a href="#" onclick="javascript:
                $.userLookup('ajax.php/users/<?php echo $user->getId(); ?>/edit',
                        function (user) {
                            $('#user-name').text(user.name);
                            $('#user-email').text(user.email);
                        });
                return false;
                "><i class="icon-user"></i>
                <span id="user-name"><?php echo $user->getName(); ?></span>
                &lt;<span id="user-email"><?php echo $user->getEmail(); ?></span>&gt;
                </a>
                <a class="action-button" style="float:none;overflow:inherit" href="#"
                    onclick="javascript:
                        $.userLookup('ajax.php/users/select/'+$('input#uid').val(),
                            function(user) {
                                $('input#uid').val(user.id);
                                $('#user-name').text(user.name);
                                $('#user-email').text('<'+user.email+'>');
                        });
                        return false;
                "><i class="icon-edit"></i> Change</a>
            </div>
        </td></tr>
        <?php
        } else { //Fallback: Just ask for email and name
            ?>
        <tr>
            <td width="160" class="required"> Email Address: </td>
            <td class="form-group form-inline has-error">
                    <input class="form-control" type="text" size=45 name="email" id="user-email"
                        autocomplete="off" autocorrect="off" value="<?php echo $info['email']; ?>" />
                <?php if($errors['email']) echo '<span class="alert alert-danger">'.$errors['email']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="160" class="required"> Full Name: </td>
            <td class="form-group form-inline has-error">
                <span style="display:inline-block;">
                    <input class="form-control" type="text" size=45 name="name" id="user-name" value="<?php echo $info['name']; ?>" /> </span>
                <?php if($errors['name']) echo '<span class="alert alert-danger">'.$errors['name']. '</span>'; ?>
            </td>
        </tr>
        <?php
        } ?>

        <?php
        if($cfg->notifyONNewStaffTicket()) {  ?>
        <tr>
            <td width="160">Ticket Notice:</td>
            <td class="form-group form-inline">
            <input type="checkbox" name="alertuser" <?php echo (!$errors || $info['alertuser'])? 'checked="checked"': ''; ?>>
            <label>Send alert to user.</label>
            </td>
        </tr>
        <?php
        } ?>
    </tbody>
    <tbody>
        <tr>
            <th colspan="2">
                <em><strong>Ticket Information &amp; Options</strong>:</em>
            </th>
        </tr>
        <tr>
            <td width="160" class="required">
                Ticket Source:
            </td>
            <td class="form-group form-inline has-error">
                <select name="source" class="form-control">
                    <option value="Phone" <?php echo ($info['source']=='Phone')?'selected="selected"':''; ?> selected="selected">Phone</option>
                    <option value="Email" <?php echo ($info['source']=='Email')?'selected="selected"':''; ?>>Email</option>
                    <option value="Other" <?php echo ($info['source']=='Other')?'selected="selected"':''; ?>>Other</option>
                </select>
                <?php if($errors['source']) echo '<span class="alert alert-danger">'.$errors['source']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="160" class="required">
                Collection:
            </td>
            <?php
                $sql='SELECT coll.collection_id, CONCAT_WS(" / ", pcoll.collection, coll.collection) as name, coll.color as color '
                    .' FROM '.COLLECTION_TABLE.' coll '
                    .' LEFT JOIN '.COLLECTION_TABLE.' pcoll ON(pcoll.collection_id=coll.collection_pid) ';
                if(($res=db_query($sql)) && db_num_rows($res)) {echo '<td class="form-group form-inline has-error">';};
                while(list($collectionId,$collection,$color)=db_fetch_row($res)) {
                    echo sprintf('<span style="display:inline-block"><input class="form-control checkbox" type="checkbox" name="collections[]" value="%d" %s><span class="label label-default" style="background-color:%s">%s</span></span>',
                        $collectionId,
                        (($info['collections'] && in_array($collectionId,$info['collections']))?'checked="checked"':''),
                        $color,
                        $collection);
                };
                ?>
                <?php if($errors['collectionId']) echo '<span class="alert alert-danger">'.$errors['collectionId']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="160">
                Department:
            </td>
            <td class="form-group form-inline">
                <select class="form-control" name="deptId">
                    <option value="" selected >&mdash; Select Department &mdash;</option>
                    <?php
                    if($depts=Dept::getDepartments()) {
                        foreach($depts as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['deptId']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>
                <?php if($errors['deptId']) echo '<span class="alert alert-danger">'.$errors['deptId']. '</span>'; ?>
            </td>
        </tr>

         <tr>
            <td width="160">
                SLA Plan:
            </td>
            <td class="form-group form-inline">
                <select class="form-control" name="slaId">
                    <option value="0" selected="selected" >&mdash; System Default &mdash;</option>
                    <?php
                    if($slas=SLA::getSLAs()) {
                        foreach($slas as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['slaId']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>
                <?php if($errors['slaId']) echo '<span class="alert alert-danger">'.$errors['slaId']. '</span>'; ?>
            </td>
         </tr>

         <tr>
            <td width="160">
                Due Date:
            </td>
            <td class="form-group form-inline">
                <input type="text" class="dp form-control" id="duedate" name="duedate" value="<?php echo Format::htmlchars($info['duedate']); ?>" size="12" autocomplete=OFF>
                <?php
                $min=$hr=null;
                if($info['time'])
                    list($hr, $min)=explode(':', $info['time']);

                echo Misc::timeDropdown($hr, $min, 'time');
                ?>
                <?php if($errors['duedate']) echo '<span class="alert alert-danger">'.$errors['duedate']; ?> &nbsp; <?php echo $errors['time']. '</span>'; ?>
                <label>Time is based on your time zone (GMT <?php echo $thisstaff->getTZoffset(); ?>)</label>
            </td>
        </tr>

        <?php
        if($thisstaff->canAssignTickets()) { ?>
        <tr>
            <td width="160">Assign To:</td>
            <td class="form-group form-inline">
                <select class="form-control" id="assignId" name="assignId">
                    <option value="0" selected="selected">&mdash; Select Staff Member OR a Team &mdash;</option>
                    <?php
                    if(($users=Staff::getAvailableStaffMembers())) {
                        echo '<OPTGROUP label="Staff Members ('.count($users).')">';
                        foreach($users as $id => $name) {
                            $k="s$id";
                            echo sprintf('<option value="%s" %s>%s</option>',
                                        $k,(($info['assignId']==$k)?'selected="selected"':''),$name);
                        }
                        echo '</OPTGROUP>';
                    }

                    if(($teams=Team::getActiveTeams())) {
                        echo '<OPTGROUP label="Teams ('.count($teams).')">';
                        foreach($teams as $id => $name) {
                            $k="t$id";
                            echo sprintf('<option value="%s" %s>%s</option>',
                                        $k,(($info['assignId']==$k)?'selected="selected"':''),$name);
                        }
                        echo '</OPTGROUP>';
                    }
                    ?>
                </select>
                <?php if($errors['assignId']) echo '<span class="alert alert-danger">'.$errors['assignId']. '</span>'; ?>
            </td>
        </tr>
        <?php } ?>
        </tbody>
        <tbody id="dynamic-form">
        <?php
            if ($form) $form->getForm()->render(true);
        ?>
        </tbody>
        <tbody> <?php
        $tform = TicketForm::getInstance()->getForm($_POST);
        if ($_POST) $tform->isValid();
        $tform->render(true);
        ?>
        </tbody>
        <tbody>
        <?php
        //is the user allowed to post replies??
        if($thisstaff->canPostReply()) { ?>
        <tr>
            <th colspan="2">
                <em><strong>Response</strong>: Optional response to the above issue.</em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
            <?php
            if(($cannedResponses=Canned::getCannedResponses())) {
                ?>
                <div style="margin-top:0.3em;margin-bottom:0.5em" class="form-group form-inline">
                    Canned Response:&nbsp;
                    <select class="form-control" id="cannedResp" name="cannedResp">
                        <option value="0" selected="selected">&mdash; Select a canned response &mdash;</option>
                        <?php
                        foreach($cannedResponses as $id =>$title) {
                            echo sprintf('<option value="%d">%s</option>',$id,$title);
                        }
                        ?>
                    </select>
                    <label><input class="form-control checkbox" type='checkbox' value='1' name="append" id="append" checked="checked">Append</label>
                </div>
            <?php
            }
                $signature = '';
                if ($thisstaff->getDefaultSignatureType() == 'mine')
                    $signature = $thisstaff->getSignature(); ?>
                <textarea class="richtext ifhtml draft draft-delete form-control"
                    data-draft-namespace="ticket.staff.response"
                    data-signature="<?php
                        echo Format::htmlchars(Format::viewableImages($signature)); ?>"
                    data-signature-field="signature" data-dept-field="deptId"
                    placeholder="Intial response for the ticket"
                    name="response" id="response" cols="21" rows="8"
                    style="width:80%;"><?php echo $info['response']; ?></textarea>
                <table border="0" cellspacing="0" cellpadding="2" width="100%">
                <?php
                if($cfg->allowAttachments()) { ?>
                    <tr><td width="100" valign="top">Attachments:</td>
                        <td>
                            <div class="canned_attachments">
                            <?php
                            if($info['cannedattachments']) {
                                foreach($info['cannedattachments'] as $k=>$id) {
                                    if(!($file=AttachmentFile::lookup($id))) continue;
                                    $hash=$file->getKey().md5($file->getId().session_id().$file->getKey());
                                    echo sprintf('<label><input type="checkbox" name="cannedattachments[]"
                                            id="f%d" value="%d" checked="checked"
                                            <a href="file.php?h=%s">%s</a>&nbsp;&nbsp;</label>&nbsp;',
                                            $file->getId(), $file->getId() , $hash, $file->getName());
                                }
                            }
                            ?>
                            </div>
                            <div class="uploads"></div>
                            <div class="file_input">
                                <input type="file" class="multifile" name="attachments[]" size="30" value="" />
                            </div>
                            <p class="help-block">Attachments must have a valid file extension (.doc, .pdf, .jpg, .jpeg, .gif, .png, .xls, .docx, .xlsx, pptx, .txt, .htm, .html) and total attachment size must be less than 16MB.</p>
                        </td>
                    </tr>
                <?php
                } ?>

            <?php
            } ?>
             <tr>
                <td width="100">Signature:</td>
                <td class="form-group form-inline">
                    <?php
                    $info['signature']=$info['signature']?$info['signature']:$thisstaff->getDefaultSignatureType();
                    ?>
                    <label><input class="form-control radio" type="radio" name="signature" value="none"> None</label>
                    <?php
                    if($thisstaff->getSignature()) { ?>
                        <label><input type="radio" name="signature" value="mine"
                            <?php echo ($info['signature']=='mine')?'checked="checked"':''; ?>> My signature</label>
                    <?php
                    } ?>
                    <label><input class="form-control radio" type="radio" name="signature" value="dept" checked="checked"
                        <?php echo ($info['signature']=='dept')?'checked="checked"':''; ?>> Dept. Signature (if set)</label>
                </td>
             </tr>
             <?php
            if($thisstaff->canCloseTickets()) { ?>
                <tr>
                    <td width="100">Ticket Status:</td>
                    <td class="form-group form-inline">
                        <input class="form-control checkbox" type="checkbox" checked="checked" name="ticket_state" value="closed" <?php echo $info['ticket_state']?'checked="checked"':''; ?>>
                        <label>Close On Response (only applicable if response is entered)</label>
                    </td>
                </tr>
             
            </table>
            </td>
        </tr>
        <?php
        } //end canPostReply
        ?>
        <tr>
            <th colspan="2">
                <em><strong>Internal Note</strong></em>
                <?php if($errors['email']) echo '<span class="alert alert-danger">'. $errors['note']. '</span>'; ?>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea class="richtext ifhtml draft draft-delete form-control"
                    placeholder="Optional internal note (recommended on assignment)"
                    data-draft-namespace="ticket.staff.note" name="note"
                    cols="21" rows="6" style="width:80%;"
                    ><?php echo $info['note']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p class="centered">
    <input class="btn btn-success" type="submit" name="submit" value="Open">
    <input class="btn btn-warning" type="reset"  name="reset"  value="Reset">
    <input class="btn btn-danger" type="button" name="cancel" value="Cancel" onclick='window.location.href="tickets.php"'>
</p>
</form>
<script type="text/javascript">
$(function() {
    $('input#user-email').typeahead({
        source: function (typeahead, query) {
            $.ajax({
                url: "ajax.php/users?q="+query,
                dataType: 'json',
                success: function (data) {
                    typeahead.process(data);
                }
            });
        },
        onselect: function (obj) {
            $('#uid').val(obj.id);
            $('#user-name').val(obj.name);
            $('#user-email').val(obj.email);
        },
        property: "/bin/true"
    });

   <?php
    // Popup user lookup on the initial page load (not post) if we don't have a
    // user selected
    if (!$_POST && !$user) {?>
    $.userLookup('ajax.php/users/lookup/form', function (user) {
        window.location.href = window.location.href+'&uid='+user.id;
     });
    <?php
    } ?>
});
</script>

