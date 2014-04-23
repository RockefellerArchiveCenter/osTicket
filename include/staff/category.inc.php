<?php
if(!defined('OSTSCPINC') || !$thisstaff || !$thisstaff->canManageFAQ()) die('Access Denied');
$info=array();
$qstr='';
if($category && $_REQUEST['a']!='add'){
    $title='Update Category: '.$category->getName();
    $action='update';
    $submit_text='Save Changes';
    $info=$category->getHashtable();
    $info['id']=$category->getId();
    $info['notes'] = Format::viewableImages($category->getNotes());
    $qstr.='&id='.$category->getId();
}else {
    $title='Add New Category';
    $action='create';
    $submit_text='Add';
    $qstr.='&a='.$_REQUEST['a'];
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);

?>
<form action="categories.php?<?php echo $qstr; ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2>FAQ Category</h2>
 <table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="2">
                <em>Category information: Public categories are published if it has published FAQ articles.</em>
            </th>
        </tr>
        <tr>
            <td width="180" class="required">Category Type:</td>
            <td class="form-group form-inline">
                <input class="form-control radio" type="radio" name="ispublic" value="1" <?php echo $info['ispublic']?'checked="checked"':''; ?>><label>Public (publish)</label>
                <input class="form-control radio" type="radio" name="ispublic" value="0" <?php echo !$info['ispublic']?'checked="checked"':''; ?>><label>Private (internal)</label>
                <?php if($errors['ispublic']) echo '<span class="alert alert-danger">' .$errors['ispublic']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td class="form-group form-inline has-error" colspan=2>
                <div><b>Category Name</b>:&nbsp;<span class="faded">Short descriptive name.</span></div>
                    <input class="form-control" type="text" size="70" name="name" value="<?php echo $info['name']; ?>">
                    <?php if($errors['name']) echo '<span class="alert alert-danger">' .$errors['name']. '</span>'; ?>
                <div>
                    <b>Category Description</b>:&nbsp;<span class="faded">Summary of the category.</span>
                    <?php if($errors['description']) echo '<span class="alert alert-danger">' .$errors['description']. '</span>'; ?></div>
                    <textarea class="richtext" name="description" cols="21" rows="12" style="width:98%;"><?php echo $info['description']; ?></textarea>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em>Internal Notes&nbsp;</em>
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
    <input class="btn btn-danger" type="button" name="cancel" value="Cancel" onclick='window.location.href="categories.php"'>
</p>
</form>
