<?php
if(!defined('OSTSTAFFINC') || !$category || !$thisstaff) die('Access Denied');

?>
<div style="width:700px;padding-top:10px; float:left;">
  <h2>Frequently Asked Questions</h2>
</div>
<div style="float:right;text-align:right;padding-top:5px;padding-right:5px;">&nbsp;</div>
<div class="clear"></div>
<br>
<div>
    <strong><?php echo $category->getName() ?></strong>
    <span>(<?php echo $category->isPublic()?'Public':'Internal'; ?>)</span>
    <time>Last updated <?php echo Format::db_date($category->getUpdateDate()); ?></time>
</div>
<div class="cat-desc">
<?php echo Format::display($category->getDescription()); ?>
</div>

<?php
$sql='SELECT faq.faq_id, question, ispublished, count(attach.file_id) as attachments '
    .' FROM '.FAQ_TABLE.' faq '
    .' LEFT JOIN '.ATTACHMENT_TABLE.' attach
         ON(attach.object_id=faq.faq_id AND attach.type=\'F\' AND attach.inline = 0) '
    .' WHERE faq.category_id='.db_input($category->getId())
    .' GROUP BY faq.faq_id ORDER BY question';
if(($res=db_query($sql)) && db_num_rows($res)) {
    echo '<table class="table table-striped">';
    while($row=db_fetch_array($res)) {
        echo sprintf('
            <tr><td><a href="faq.php?id=%d" class="previewfaq">%s <span>- %s</span></a></td>',
            $row['faq_id'],$row['question'],$row['ispublished']?'Published':'Internal');
    }
    echo '
         </table>';
}else {
    echo '<strong>Category does not have FAQs</strong>';
}
?>
<?php
if($thisstaff->canManageFAQ()) {
    echo sprintf('<div class="text-center"><a href="categories.php?id=%d" class="btn btn-warning editCategory">Edit Category</a>
             <a href="categories.php" class="btn btn-danger deleteCategory">Delete Category</a>
             <a href="faq.php?cid=%d&a=add" class="btn btn-success newFAQ">Add New FAQ</a></div>',
            $category->getId(),
            $category->getId());
}
?>