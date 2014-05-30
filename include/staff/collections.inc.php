<?php
if(!defined('OSTADMININC') || !$thisstaff->isAdmin()) die('Access Denied');

$qstr='';
$sql='SELECT collection.* '
    .', IF(pcollection.collection_pid IS NULL, collection.collection, CONCAT_WS(" / ", pcollection.collection, collection.collection)) as name '
    .', dept.dept_name as department '
    .' FROM '.COLLECTION_TABLE.' collection '
    .' LEFT JOIN '.COLLECTION_TABLE.' pcollection ON (pcollection.collection_id=collection.collection_pid) '
    .' LEFT JOIN '.DEPT_TABLE.' dept ON (dept.dept_id=collection.dept_id) '
    .' LEFT JOIN '.TICKET_PRIORITY_TABLE.' pri ON (pri.priority_id=collection.priority_id) ';
$sql.=' WHERE 1';
$sortOptions=array('name'=>'name','status'=>'collection.isactive','type'=>'collection.ispublic',
                   'dept'=>'department','updated'=>'collection.updated');
$orderWays=array('DESC'=>'DESC','ASC'=>'ASC');
$sort=($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])])?strtolower($_REQUEST['sort']):'name';
//Sorting options...
if($sort && $sortOptions[$sort]) {
    $order_column =$sortOptions[$sort];
}
$order_column=$order_column?$order_column:'collection.collection';

if($_REQUEST['order'] && $orderWays[strtoupper($_REQUEST['order'])]) {
    $order=$orderWays[strtoupper($_REQUEST['order'])];
}
$order=$order?$order:'ASC';

if($order_column && strpos($order_column,',')){
    $order_column=str_replace(','," $order,",$order_column);
}
$x=$sort.'_sort';
$$x=' class="'.strtolower($order).'" ';
$order_by="$order_column $order ";
$total=db_count('SELECT count(*) FROM '.COLLECTION_TABLE.' collection ');
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total, $page, PAGE_LIMIT);
$pageNav->setURL('collections.php',$qstr.'&sort='.urlencode($_REQUEST['sort']).'&order='.urlencode($_REQUEST['order']));
//Ok..lets roll...create the actual query
$qstr.='&order='.($order=='DESC'?'ASC':'DESC');
$query="$sql GROUP BY collection.collection_id ORDER BY $order_by LIMIT ".$pageNav->getStart().",".$pageNav->getLimit();
$res=db_query($query);
if($res && ($num=db_num_rows($res)))
    $showing=$pageNav->showing().' collections';
else
    $showing='No collections found!';

?>
<div style="width:700px;padding-top:5px; float:left;">
 <h2>Collections</h2>
 </div>
<div style="float:right;text-align:right;padding-top:5px;padding-right:5px;">
    <b><a href="collections.php?a=add" class="btn btn-default pull-right">Add New Collection</a></b></div>
<div class="clear"></div>
<form action="collections.php" method="POST" name="collections">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="mass_process" >
<input type="hidden" id="action" name="a" value="" >
 <table class="table table-striped" border="0" cellspacing="1" cellpadding="0" width="100%">
    <caption><?php echo $showing; ?></caption>
    <thead>
        <tr>
            <th width="7">&nbsp;</th>        
            <th width="320"><a <?php echo $name_sort; ?> href="collections.php?<?php echo $qstr; ?>&sort=name">Collection</a></th>
            <th width="80"><a  <?php echo $status_sort; ?> href="collections.php?<?php echo $qstr; ?>&sort=status">Status</a></th>
            <th width="100"><a  <?php echo $type_sort; ?> href="collections.php?<?php echo $qstr; ?>&sort=type">Type</a></th>
            <th width="200"><a  <?php echo $dept_sort; ?> href="collections.php?<?php echo $qstr; ?>&sort=dept">Department</a></th>
            <th width="150" nowrap><a  <?php echo $updated_sort; ?>href="collections.php?<?php echo $qstr; ?>&sort=updated">Last Updated</a></th>
        </tr>
    </thead>
    <tbody>
    <?php
        $total=0;
        $ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
        if($res && db_num_rows($res)):
            while ($row = db_fetch_array($res)) {
                $sel=false;
                if($ids && in_array($row['collection_id'],$ids))
                    $sel=true;
                ?>
            <tr id="<?php echo $row['collection_id']; ?>">
                <td width=7px>
                  <input type="checkbox" class="ckb" name="ids[]" value="<?php echo $row['collection_id']; ?>" 
                            <?php echo $sel?'checked="checked"':''; ?>>
                </td>
                <td style="border-left: 10px solid <?php echo $row['color']; ?>"><a href="collections.php?id=<?php echo $row['collection_id']; ?>"><?php echo $row['name']; ?></a>&nbsp;</td>
                <td><?php echo $row['isactive']?'Active':'<b>Disabled</b>'; ?></td>
                <td><?php echo $row['ispublic']?'Public':'<b>Private</b>'; ?></td>
                <td><a href="departments.php?id=<?php echo $row['dept_id']; ?>"><?php echo $row['department']; ?></a></td>
                <td>&nbsp;<?php echo Format::db_datetime($row['updated']); ?></td>
            </tr>
            <?php
            } //end of while.
        endif; ?>
    <tfoot>
     <tr>
        <td colspan="7">
            <?php if($res && $num){ ?>
            Select:&nbsp;
            <a id="selectAll" href="#ckb">All</a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb">None</a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb">Toggle</a>&nbsp;&nbsp;
            <?php }else{
                echo 'No collections found';
            } ?>
        </td>
     </tr>
    </tfoot>
</table>
<?php
if($res && $num): //Show options..
    echo '<ul class="pagination">'.$pageNav->getPageLinks().'</ul>';
?>
<p class="centered" id="actions">
    <input class="button btn btn-success" type="submit" name="enable" value="Enable" >
    <input class="button btn btn-warning" type="submit" name="disable" value="Disable">
    <input class="button btn btn-danger" type="submit" name="delete" value="Delete">
</p>
<?php
endif;
?>
</form>

<div style="display:none;" class="dialog" id="confirm-action">
    <h3>Please Confirm</h3>
    <a class="close" href=""><i class="icon-remove-circle"></i></a>
    <hr/>
    <p class="confirm-action" style="display:none;" id="enable-confirm">
        Are you sure want to <b>enable</b> selected collections?
    </p>
    <p class="confirm-action" style="display:none;" id="disable-confirm">
        Are you sure want to <b>disable</b> selected collections?
    </p>
    <p class="confirm-action" style="display:none;" id="delete-confirm">
        <font color="red"><strong>Are you sure you want to DELETE selected collections?</strong></font>
        <br><br>Deleted collections CANNOT be recovered.
    </p>
    <div>Please confirm to continue.</div>
    <hr style="margin-top:1em"/>
    <p class="full-width">
        <span class="buttons" style="float:left">
            <input type="button" value="No, Cancel" class="btn btn-default close">
        </span>
        <span class="buttons" style="float:right">
            <input type="button" value="Yes, Do it!" class="btn btn-primary confirm">
        </span>
     </p>
    <div class="clear"></div>
</div>

