<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');

$matches=Filter::getSupportedMatches();
$match_types=Filter::getSupportedMatchTypes();

$info=array();
$qstr='';
if($filter && $_REQUEST['a']!='add'){
    $title='Update Filter';
    $action='update';
    $submit_text='Save Changes';
    $info=array_merge($filter->getInfo(),$filter->getFlatRules());
    $info['id']=$filter->getId();
    $qstr.='&id='.$filter->getId();
}else {
    $title='Add New Filter';
    $action='add';
    $submit_text='Add Filter';
    $info['isactive']=isset($info['isactive'])?$info['isactive']:0;
    $qstr.='&a='.urlencode($_REQUEST['a']);
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="filters.php?<?php echo $qstr; ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2>Ticket Filter</h2>
 <table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em>Filters are executed based on execution order. Filter can target specific ticket source.</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="180" class="required">
              Filter Name:
            </td>
            <td class="form-group form-inline has-error">
                <input class="form-control" type="text" size="30" name="name" value="<?php echo $info['name']; ?>">
                <?php if($errors['name']) echo '<span class="alert alert-danger">' .$errors['name']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
              Execution Order:
            </td>
            <td class="form-group form-inline has-error">
                <input class="form-control" type="text" size="6" name="execorder" value="<?php echo $info['execorder']; ?>">
                <label>(1...99 )</label>
                <?php if($errors['execorder']) echo '<span class="alert alert-danger">' .$errors['execorder']. '</span>'; ?>
                <input class="form-control checkbox" type="checkbox" name="stop_onmatch" value="1" <?php echo $info['stop_onmatch']?'checked="checked"':''; ?> >
                <label>Stop processing further on match!</label>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                Filter Status:
            </td>
            <td class="form-group form-inline has-error">
                <input class="form-control radio" type="radio" name="isactive" value="1" <?php echo $info['isactive']?'checked="checked"':''; ?>><label>Active</label>
                <input class="form-control radio" type="radio" name="isactive" value="0" <?php echo !$info['isactive']?'checked="checked"':''; ?>><label>Disabled</label>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                Target:
            </td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="target">
                   <option value="">&mdash; Select a Target &dash;</option>
                   <?php
                   foreach(Filter::getTargets() as $k => $v) {
                       echo sprintf('<option value="%s" %s>%s</option>',
                               $k, (($k==$info['target'])?'selected="selected"':''), $v);
                    }
                    $sql='SELECT email_id,email,name FROM '.EMAIL_TABLE.' email ORDER by name';
                    if(($res=db_query($sql)) && db_num_rows($res)) {
                        echo '<OPTGROUP label="Specific System Email">';
                        while(list($id,$email,$name)=db_fetch_row($res)) {
                            $selected=($info['email_id'] && $id==$info['email_id'])?'selected="selected"':'';
                            if($name)
                                $email=Format::htmlchars("$name <$email>");
                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$email);
                        }
                        echo '</OPTGROUP>';
                    }
                    ?>
                </select>
                <?php if($errors['target']) echo '<span class="alert alert-danger">' .$errors['target']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Filter Rules</strong>: Rules are applied based on the criteria.</em>
                <?php if($errors['rules']) echo '<span class="alert alert-danger">' .$errors['rules']. '</span>'; ?>
            </th>
        </tr>
        <tr>
            <td class="form-group form-inline" colspan="2">
               <em>Rules Matching Criteria:</em>
                <input class="form-control radio" type="radio" name="match_all_rules" value="1" <?php echo $info['match_all_rules']?'checked="checked"':''; ?>>
                <label>Match All</label>
                <input class="form-control radio" type="radio" name="match_all_rules" value="0" <?php echo !$info['match_all_rules']?'checked="checked"':''; ?>>
                <label>Match Any</label>
                <p class="help-block">Case-insensitive comparison</p>
            </td>
        </tr>
        <?php
        $n=($filter?$filter->getNumRules():0)+2; //2 extra rules of unlimited.
        for($i=1; $i<=$n; $i++){ ?>
        <tr id="r<?php echo $i; ?>">
            <td  class="form-group form-inline" colspan="2">
                <div>
                    <select class="form-control" name="rule_w<?php echo $i; ?>">
                        <option value="">&mdash; Select One &dash;</option>
                        <?php
                        foreach ($matches as $group=>$ms) { ?>
                            <optgroup label="<?php echo $group; ?>"><?php
                            foreach ($ms as $k=>$v) {
                                $sel=($info["rule_w$i"]==$k)?'selected="selected"':'';
                                echo sprintf('<option value="%s" %s>%s</option>',$k,$sel,$v);
                            } ?>
                        </optgroup>
                        <?php } ?>
                    </select>
                    <select class="form-control" name="rule_h<?php echo $i; ?>">
                        <option value="0">&mdash; Select One &dash;</option>
                        <?php
                        foreach($match_types as $k=>$v){
                            $sel=($info["rule_h$i"]==$k)?'selected="selected"':'';
                            echo sprintf('<option value="%s" %s>%s</option>',$k,$sel,$v);
                        }
                        ?>
                    </select>
                    <input class="form-control" type="text" size="60" name="rule_v<?php echo $i; ?>" value="<?php echo $info["rule_v$i"]; ?>">
                    <?php if($errors['rule_$i']) echo '<span class="alert alert-danger">' .$errors["rule_$i"]. '</span>'; ?>
                <?php
                if($info["rule_w$i"] || $info["rule_h$i"] || $info["rule_v$i"]){ ?>
                <div style="float:right;text-align:right;padding-right:20px;"><a href="#" class="clearrule">(clear)</a></div>
                <?php
                } ?>
                </div>
            </td>
        </tr>
        <?php
            if($i>=25) //Hardcoded limit of 25 rules...also see class.filter.php
               break;
        } ?>
        <tr>
            <th colspan="2">
                <em><strong>Filter Actions</strong>: Can be overridden by other filters depending on processing order.&nbsp;</em>
            </th>
        </tr>
        <tr>
            <td width="180">
                Reject Ticket:
            </td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="reject_ticket" value="1" <?php echo $info['reject_ticket']?'checked="checked"':''; ?> >
                    <label>Reject Ticket (all other actions and filters are ignored)</label>
            </td>
        </tr>
        <tr>
            <td width="180">
                Reply-To Email:
            </td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="use_replyto_email" value="1" <?php echo $info['use_replyto_email']?'checked="checked"':''; ?> >
                <label>Use Reply-To Email (if available)</label>
            </td>
        </tr>
        <tr>
            <td width="180">
                Ticket auto-response:
            </td>
            <td class="form-group form-inline">
                <input  class="form-control checkbox" type="checkbox" name="disable_autoresponder" value="1" <?php echo $info['disable_autoresponder']?'checked="checked"':''; ?> >
                <label>Disable auto-response. (Override Dept. settings)</label>
            </td>
        </tr>
        <tr>
            <td width="180">
                Canned Response:
            </td>
                <td class="form-group form-inline">
                <select class="form-control" name="canned_response_id">
                    <option value="">&mdash; None &mdash;</option>
                    <?php
                    $sql='SELECT canned_id,title FROM '.CANNED_TABLE
                        .' WHERE isenabled ORDER by title';
                    if ($res=db_query($sql)) {
                        while (list($id,$title)=db_fetch_row($res)) {
                            $selected=($info['canned_response_id'] &&
                                    $id==$info['canned_response_id'])
                                ? 'selected="selected"' : '';
                            echo sprintf('<option value="%d" %s>%s</option>',
                                $id, $selected, $title);
                        }
                    }
                    ?>
                </select>
                <p class="help-block">Automatically respond with this canned response</p>
            </td>
        </tr>
        <tr>
            <td width="180">
                Department:
            </td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="dept_id">
                    <option value="">&mdash; Default &mdash;</option>
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
                Priority:
            </td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="priority_id">
                    <option value="">&mdash; Default &mdash;</option>
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
                <p class="help-block">Overrides department's priority</p>
            </td>
        </tr>
        <tr>
            <td width="180">
                SLA Plan:
            </td>
            <td class="form-group form-inline">
                <select  class="form-control" name="sla_id">
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
                <?php if($errors['sla_id']) echo '<span class="alert alert-danger">' .$errors['sla_id']. '</span>'; ?>
                <p class="help-block">Overrides department's SLA</p>
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
            <th colspan="2">
                <em><strong>Admin Notes</strong>: Internal notes.</em>
            </th>
        </tr>
        <tr>
            <td class="form-group form-inline" colspan="2">
                <textarea class="richtext no-bar" name="notes" cols="21"
                    rows="8" style="width: 80%;"><?php echo $info['notes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p class="centered">
    <input class="btn btn-success" type="submit" name="submit" value="<?php echo $submit_text; ?>">
    <input class="btn btn-warning" type="reset"  name="reset"  value="Reset">
    <input class="btn btn-danger" type="button" name="cancel" value="Cancel" onclick='window.location.href="filters.php"'>
</p>
</form>
