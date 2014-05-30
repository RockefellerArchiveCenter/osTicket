<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
$info=array();
$qstr='';
if($collection && $_REQUEST['a']!='add') {
    $title='Update Collection';
    $action='update';
    $submit_text='Save Changes';
    $info=$collection->getInfo();
    $info['id']=$collection->getId();
    $info['pid']=$collection->getPid();
    $qstr.='&id='.$collection->getId();
} else {
    $title='Add New Collection';
    $action='create';
    $submit_text='Add Collection';
    $info['isactive']=isset($info['isactive'])?$info['isactive']:1;
    $info['ispublic']=isset($info['ispublic'])?$info['ispublic']:1;
    $qstr.='&a='.$_REQUEST['a'];
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="collections.php?<?php echo $qstr; ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2>Collection</h2>
 <table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em>Collection Information</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="180" class="required">
               Name:
            </td>
            <td class="form-group form-inline has-error">
                <input class="form-control" type="text" size="30" name="collection" value="<?php echo $info['collection']; ?>">
                <?php if($errors['collection']) echo '<span class="alert alert-danger">' .$errors['collection']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                Status:
            </td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="isactive" value="1" <?php echo $info['isactive']?'checked="checked"':''; ?>>
                <label>Active</label>
                <input class="form-control radio" type="radio" name="isactive" value="0" <?php echo !$info['isactive']?'checked="checked"':''; ?>>
                <label>Disabled</label>
            </td>
        </tr>
        <tr>
            <td width="180">
               Color:
               
            </td>
            <td class="form-group form-inline">
                <div class="pull-left" style="margin-right:.5em;width:1em;height:2.5em;background-color:<?php echo $info['color']; ?>">&nbsp;</div>
                <input class="form-control" type="text" size="7" name="color" value="<?php echo $info['color']; ?>">
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                Type:
            </td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="ispublic" value="1" <?php echo $info['ispublic']?'checked="checked"':''; ?>>
                <label>Public</label>
                <input class="form-control radio" type="radio" name="ispublic" value="0" <?php echo !$info['ispublic']?'checked="checked"':''; ?>>
                <label>Private/Internal</label>
            </td>
        </tr>
        <tr>
            <td width="180">
                Parent Topic:
            </td>
            <td class="form-group form-inline">
                <select class="form-control" name="pid">
                    <option value="">&mdash; Select Parent Topic &mdash;</option>
                    <?php
                    $sql='SELECT collection_id, collection FROM '.COLLECTION_TABLE
                        .' WHERE collection_pid=0 '
                        .' ORDER by collection';
                    if(($res=db_query($sql)) && db_num_rows($res)) {
                        while(list($id, $name)=db_fetch_row($res)) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, (($info['pid'] && $id==$info['pid'])?'selected="selected"':'') ,$name);
                        }
                    }
                    ?>
                </select> 
                <p class="help-block">optional</p>
                <?php if($errors['pid']) echo '<span class="alert alert-danger">' .$errors['pid']. '</span>'; ?>
            </td>
        </tr>

        <tr><th colspan="2"><em>New ticket options</em></th></tr>
       <tr>
           <td><strong>Custom Form</strong>:</td>
           <td class="form-group form-inline">
           <select class="form-control" name="form_id">
               <option value="0">&mdash; No Extra Fields &mdash;</option>
               <?php foreach (DynamicForm::objects()->filter(array('type'=>'G')) as $group) { ?>
                   <option value="<?php echo $group->get('id'); ?>"
                       <?php if ($group->get('id') == $info['form_id'])
                            echo 'selected="selected"'; ?>>
                       <?php echo $group->get('title'); ?>
                   </option>
               <?php } ?>
               </select>
               <p class="help-block">Extra information for tickets associated with this collection</p>
               <?php if($errors['form_id']) echo '<span class="alert alert-danger">' .$errors['form_id']. '</span>'; ?>
           </td>
       </tr>
        <tr>
            <td width="180">
                Priority:
            </td>
            <td class="form-group form-inline">
                <select class="form-control" name="priority_id">
                    <option value="">&mdash; System Default &mdash;</option>
                    <?php
                    $sql='SELECT priority_id,priority_desc FROM '.PRIORITY_TABLE.' pri ORDER by priority_urgency DESC';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while(list($id,$name)=db_fetch_row($res)){
                            $selected=($info['priority_id'] && $id==$info['priority_id'])?'selected="selected"':'';
                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$name);
                        }
                    }
                    ?>
                </select>
                <?php if($errors['priority_id']) echo '<span class="alert alert-danger">' .$errors['priority_id']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                Department:
            </td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="dept_id">
                    <option value="">&mdash; Select Department &mdash;</option>
                    <?php
                    $sql='SELECT dept_id,dept_name FROM '.DEPT_TABLE.' dept ORDER by dept_name';
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
                SLA Plan:
            </td>
            <td class="form-group form-inline">
                <select class="form-control" name="sla_id">
                    <option value="0">&mdash; Department's Default &mdash;</option>
                    <?php
                    if($slas=SLA::getSLAs()) {
                        foreach($slas as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['sla_id']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>
                <?php if($errors['sla_id']) echo '<span class="alert alert-danger">' .$errors['sla_id']. '</span>'; ?>
                <p class="help-block">Overrides department's SLA</p>
            </td>
        </tr>
        <tr>
            <td width="180">Thank-you Page:</td>
            <td class="form-group form-inline">
                <select class="form-control" name="page_id">
                    <option value="">&mdash; System Default &mdash;</option>
                    <?php
                    if(($pages = Page::getActiveThankYouPages())) {
                        foreach($pages as $page) {
                            if(strcasecmp($page->getType(), 'thank-you')) continue;
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $page->getId(),
                                    ($info['page_id']==$page->getId())?'selected="selected"':'',
                                    $page->getName());
                        }
                    }
                    ?>
                </select>
                <?php if($errors['page_id']) echo '<span class="alert alert-danger">' .$errors['page_id']. '</span>'; ?>
                <p class="help-block">Overrides global setting. Applies to web tickets only.</p>
            </td>
        </tr>
        <tr>
            <td width="180">
                Auto-assign To:
            </td>
            <td class="form-group form-inline">
                <select class="form-control" name="assign">
                    <option value="0">&mdash; Unassigned &mdash;</option>
                    <?php
                    $sql=' SELECT staff_id,CONCAT_WS(", ",lastname,firstname) as name '.
                         ' FROM '.STAFF_TABLE.' WHERE isactive=1 ORDER BY name';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        echo '<OPTGROUP label="Staff Members">';
                        while (list($id,$name) = db_fetch_row($res)){
                            $k="s$id";
                            $selected = ($info['assign']==$k || $info['staff_id']==$id)?'selected="selected"':'';
                            ?>
                            <option value="<?php echo $k; ?>"<?php echo $selected; ?>><?php echo $name; ?></option>

                        <?php }
                        echo '</OPTGROUP>';
                    }
                    $sql='SELECT team_id, name FROM '.TEAM_TABLE.' WHERE isenabled=1';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        echo '<OPTGROUP label="Teams">';
                        while (list($id,$name) = db_fetch_row($res)){
                            $k="t$id";
                            $selected = ($info['assign']==$k || $info['team_id']==$id)?'selected="selected"':'';
                            ?>
                            <option value="<?php echo $k; ?>"<?php echo $selected; ?>><?php echo $name; ?></option>
                        <?php
                        }
                        echo '</OPTGROUP>';
                    }
                    ?>
                </select>
                <?php if($errors['assign']) echo '<span class="alert alert-danger">' .$errors['assign']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">
                Ticket auto-response:
            </td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="noautoresp" value="1" <?php echo $info['noautoresp']?'checked="checked"':''; ?> >
                <label>Disable new ticket auto-response for this collection (Overrides Dept. settings).</label>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Admin Notes</strong>: Internal notes about the collection.&nbsp;</em>
            </th>
        </tr>
        <tr>
            <td colspan="2">
                <textarea class="richtext no-bar" name="notes" cols="21"
                    rows="8" style="width: 80%;"><?php echo $info['notes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p class="centered">
    <input class="btn btn-success" type="submit" name="submit" value="<?php echo $submit_text; ?>">
    <input class="btn btn-warning" type="reset"  name="reset"  value="Reset">
    <input class="btn btn-danger" type="button" name="cancel" value="Cancel" onclick='window.location.href="collections.php"'>
</p>
</form>
