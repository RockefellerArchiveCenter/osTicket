<?php
if(!defined('OSTSCPINC') || !$thisstaff) die('Access Denied');
$info=array();
$qstr='';
if($canned && $_REQUEST['a']!='add'){
    $title='Update Canned Response';
    $action='update';
    $submit_text='Save Changes';
    $info=$canned->getInfo();
    $info['id']=$canned->getId();
    $qstr.='&id='.$canned->getId();
    // Replace cid: scheme with downloadable URL for inline images
    $info['response'] = $canned->getResponseWithImages();
    $info['notes'] = Format::viewableImages($info['notes']);
}else {
    $title='Add New Canned Response';
    $action='create';
    $submit_text='Add Response';
    $info['isenabled']=isset($info['isenabled'])?$info['isenabled']:1;
    $qstr.='&a='.$_REQUEST['a'];
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);

?>
<form action="canned.php?<?php echo $qstr; ?>" method="post" id="save" enctype="multipart/form-data">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2>Canned Response</h2>
 <table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr><td></td><td></td></tr> <!-- For fixed table layout -->
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em>Canned response settings</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="180" class="required">Status:</td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="isenabled" value="1" <?php echo $info['isenabled']?'checked="checked"':''; ?>><label>Active</label>
                <input class="form-control radio" type="radio" name="isenabled" value="0" <?php echo !$info['isenabled']?'checked="checked"':''; ?>><label>Disabled</label>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">Department:</td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="dept_id">
                    <option value="0">&mdash; All Departments &mdash;</option>
                    <?php
                    $sql='SELECT dept_id, dept_name FROM '.DEPT_TABLE.' dept ORDER by dept_name';
                    if(($res=db_query($sql)) && db_num_rows($res)) {
                        while(list($id,$name)=db_fetch_row($res)) {
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
            <th colspan="2">
                <em><strong>Canned Response</strong>: Make the title short and clear.&nbsp;</em>
            </th>
        </tr>
        <tr>
            <td colspan="2" class="form-group has-error">
                <div><b>Title</b></div>
                <input class="form-control" type="text" size="70" name="title" value="<?php echo $info['title']; ?>"><?php if($errors['title']) echo '<span class="alert alert-danger">' .$errors['title']. '</span>'; ?>
                <div style="margin-bottom:0.5em"><b>Canned Response</b><?php if($errors['response']) echo '<span class="alert alert-danger">' .$errors['response']. '</span>'; ?>
                    &nbsp;(<a class="tip" href="ticket_variables">Supported Variables</a>)
                    </div>
                <textarea name="response" class="richtext draft draft-delete" cols="21" rows="12"
                    data-draft-namespace="canned"
                    data-draft-object-id="<?php if (isset($canned)) echo $canned->getId(); ?>"
                    style="width:98%;" class="richtext draft"><?php
                        echo $info['response']; ?></textarea>
               <div><b>Canned Attachments</b> (optional) <?php if($errors['files']) echo '<span class="alert alert-danger">' .$errors['files']. '</span>'; ?></div>
                <?php
                if($canned && ($files=$canned->attachments->getSeparates())) {
                    echo '<div id="canned_attachments"><span class="faded">Uncheck to delete the attachment on submit</span><br>';
                    foreach($files as $file) {
                        $hash=$file['key'].md5($file['id'].session_id().strtolower($file['key']));
                        echo sprintf('<label><input type="checkbox" name="files[]" id="f%d" value="%d" checked="checked">
                                      <a href="file.php?h=%s">%s</a>&nbsp;&nbsp;</label>&nbsp;',
                                      $file['id'], $file['id'], $hash, $file['name']);
                    }
                    echo '</div><br>';

                }
                //Hardcoded limit... TODO: add a setting on admin panel - what happens on tickets page??
                if(count($files)<10) {
                ?>
                <div>
                    <input type="file" name="attachments[]" value=""/>
                </div>
                <?php
                }?>
                <p class="help-block">Attachments must have a valid file extension (.doc, .pdf, .jpg, .jpeg, .gif, .png, .xls, .docx, .xlsx, pptx, .txt, .htm, .html) and total attachment size must be less than 16MB. You can upload 10 attachments per canned response</p>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Internal Notes</strong>: Notes about the canned response.&nbsp;</em>
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
 <?php if ($canned && $canned->getFilters()) { ?>
    <br/>
    <div class="alert alert-warning">Canned response is in use by email filter(s): <?php
    echo implode(', ', $canned->getFilters()); ?></div>
 <?php } ?>
<p class="centered">
    <input class="btn btn-success" type="submit" name="submit" value="<?php echo $submit_text; ?>">
    <input class="btn btn-warning" type="reset"  name="reset"  value="Reset" onclick="javascript:
        $(this.form).find('textarea.richtext')
            .redactor('deleteDraft');
        location.reload();" />
    <input class="btn btn-danger" type="button" name="cancel" value="Cancel" onclick='window.location.href="canned.php"'>
</p>
</form>
