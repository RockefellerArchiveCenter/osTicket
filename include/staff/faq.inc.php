<?php
if(!defined('OSTSCPINC') || !$thisstaff || !$thisstaff->canManageFAQ()) die('Access Denied');
$info=array();
$qstr='';
if($faq){
    $title='Update FAQ: '.$faq->getQuestion();
    $action='update';
    $submit_text='Save Changes';
    $info=$faq->getHashtable();
    $info['id']=$faq->getId();
    $info['collections']=$faq->getCollectionsIds();
    $info['answer']=Format::viewableImages($faq->getAnswer());
    $info['notes']=Format::viewableImages($faq->getNotes());
    $qstr='id='.$faq->getId();
}else {
    $title='Add New FAQ';
    $action='create';
    $submit_text='Add FAQ';
    if($category) {
        $qstr='cid='.$category->getId();
        $info['category_id']=$category->getId();
    }
}
//TODO: Add attachment support.
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="faq.php?<?php echo $qstr; ?>" method="post" id="save" enctype="multipart/form-data">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2>FAQ</h2>
 <table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr><td></td><td></td></tr> <!-- For fixed table layout -->
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="2">
                <em>FAQ Information</em>
            </th>
        </tr>
        <tr>
            <td colspan="2" class="form-group form-inline has-error">
                <b>Question</b>
                <input class="form-control" type="text" size="70" name="question" value="<?php echo $info['question']; ?>">
                <?php if($errors['question']) echo '<span class="alert alert-danger">' .$errors['question']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="form-group form-inline has-error">
                <div><b>Category Listing</b>:&nbsp;<span class="faded">FAQ category the question belongs to.</span></div>
                <select class="form-control" name="category_id" style="width:350px;">
                    <option value="0">Select FAQ Category </option>
                    <?php
                    $sql='SELECT category_id, name, ispublic FROM '.FAQ_CATEGORY_TABLE;
                    if(($res=db_query($sql)) && db_num_rows($res)) {
                        while($row=db_fetch_array($res)) {
                            echo sprintf('<option value="%d" %s>%s (%s)</option>',
                                    $row['category_id'],
                                    (($info['category_id']==$row['category_id'])?'selected="selected"':''),
                                    $row['name'],
                                    ($info['ispublic']?'Public':'Internal'));
                        }
                    }
                   ?>
                </select>
                <?php if($errors['category_id']) echo '<span class="alert alert-danger">' .$errors['category_id']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="form-group form-inline has-error">
                <div><b>Listing Type</b>:&nbsp;
                <span class="faded">Published questions are listed on public knowledgebase if the parent category is public.</span></div>
                <input class="form-control radio" type="radio" name="ispublished" value="1" <?php echo $info['ispublished']?'checked="checked"':''; ?>><label>Public (publish)</label>
                <input type="radio" name="ispublished" value="0" <?php echo !$info['ispublished']?'checked="checked"':''; ?>><label>Internal (private)</label>
                <?php if($errors['ispublished']) echo '<span class="alert alert-danger">' .$errors['ispublished']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="form-group form-inline has-error">
                <div>
                    <b>Answer</b>
                    <?php if($errors['answer']) echo '<span class="alert alert-danger">' .$errors['answer']. '</span>'; ?>
                </div>
                <textarea class="form-control" name="answer" cols="21" rows="12"
                    style="width:98%;" class="richtext draft"
                    data-draft-namespace="faq"
                    data-draft-object-id="<?php if (isset($faq)) echo $faq->getId(); ?>"
                    ><?php echo $info['answer']; ?></textarea>
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <div><b>Attachments</b> (optional) <?php if($errors['files']) echo '<span class="alert alert-danger">' .$errors['files']. '</span>'; ?></div>
                <?php
                if($faq && ($files=$faq->attachments->getSeparates())) {
                    echo '<div class="faq_attachments"><span class="faded">Uncheck to delete the attachment on submit</span><br>';
                    foreach($files as $file) {
                        $hash=$file['key'].md5($file['id'].session_id().strtolower($file['key']));
                        echo sprintf('<label><input type="checkbox" name="files[]" id="f%d" value="%d" checked="checked">
                                      <a href="file.php?h=%s">%s</a>&nbsp;&nbsp;</label>&nbsp;',
                                      $file['id'], $file['id'], $hash, $file['name']);
                    }
                    echo '</div><br>';
                }
                ?>
                <div class="faded">Select files to upload.</div>
                <div class="uploads"></div>
                <div class="file_input">
                    <input type="file" class="multifile" name="attachments[]" size="30" value="" />
                </div>
                <p class="help-block">Attachments must have a valid file extension (.doc, .pdf, .jpg, .jpeg, .gif, .png, .xls, .docx, .xlsx, pptx, .txt, .htm, .html) and total attachment size must be less than 16MB.</p>
            </td>
        </tr>
        <?php
        $sql='SELECT coll.collection_id, CONCAT_WS(" / ", pcoll.collection, coll.collection) as name, coll.color as color '
            .' FROM '.COLLECTION_TABLE.' coll '
            .' LEFT JOIN '.COLLECTION_TABLE.' pcoll ON(pcoll.collection_id=coll.collection_pid) ';
        if(($res=db_query($sql)) && db_num_rows($res)) { ?>
        <tr>
            <th colspan="2">
                <em><strong>Collections</strong>: Check all collections related to this FAQ.</em>
            </th>
        </tr>
        <tr><td  class="form-group form-inline" colspan="2">
            <?php
            while(list($collectionId,$collection,$color)=db_fetch_row($res)) {
                echo sprintf('<input class="form-control checkbox" type="checkbox" name="collections[]" value="%d" %s><span class="label label-default" style="background-color:%s">%s</span>',
                        $collectionId,
                        (($info['collections'] && in_array($collectionId,$info['collections']))?'checked="checked"':''),
                        $color,
                        $collection);
            }
             ?>
            </td>
        </tr>
        <?php
        } ?>
        <tr>
            <th colspan="2">
                <em><strong>Internal Notes</strong>: &nbsp;</em>
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
    <input class="btn btn-warning" type="reset"  name="reset"  value="Reset" onclick="javascript:
        $(this.form).find('textarea.richtext')
            .redactor('deleteDraft');
        location.reload();" />
    <input class="btn btn-danger" type="button" name="cancel" value="Cancel" onclick='window.location.href="faq.php?<?php echo $qstr; ?>"'>
</p>
</form>
