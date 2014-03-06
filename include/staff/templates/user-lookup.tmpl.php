<div id="the-lookup-form">
<h3><?php echo $info['title']; ?></h3>
<b><a class="close" href="#"><i class="icon-remove-circle"></i></a></b>
<hr/>
<div><p class="alert alert-info"><i class="icon-info-sign"></i>&nbsp; Search existing users or add a new user.</p></div>
<div style="margin-bottom:10px;"><input type="text" class="form-control" placeholder="Search by email, phone or name" id="user-search" autocorrect="off" autocomplete="off"/></div>
<?php
if ($info['error']) {
    echo sprintf('<p class="alert alert-danger">%s</p>', $info['error']);
} elseif ($info['msg']) {
    echo sprintf('<p class="alert alert-info">%s</p>', $info['msg']);
} ?>
<div id="selected-user-info" style="display:<?php echo $user ? 'block' :'none'; ?>;margin:5px;">
<form method="get" class="user" action="#users/lookup">
    <input type="hidden" id="user-id" name="id" value="<?php echo $user ? $user->getId() : 0; ?>"/>
    <i class="icon-user icon-4x pull-left icon-border"></i>
    <a class="btn btn-sm btn-default pull-right" id="unselect-user" href="#"></i> Add New User</a>
    <div><strong id="user-name"><?php echo $user ? Format::htmlchars($user->getName()->getOriginal()) : ''; ?></strong></div>
    <div>&lt;<span id="user-email"><?php echo $user ? $user->getEmail() : ''; ?></span>&gt;</div>
<?php if ($user) { ?>
    <table class="table">
<?php foreach ($user->getDynamicData() as $entry) { ?>
    <tr><td colspan="2"><strong><?php
         echo $entry->getForm()->get('title'); ?></strong></td></tr>
<?php foreach ($entry->getAnswers() as $a) { ?>
    <tr><td><?php echo Format::htmlchars($a->getField()->get('label'));
         ?>:</td>
    <td><?php echo $a->display(); ?></td>
    </tr>
<?php }
}
?>
</table>
<?php } ?>
    <div class="clear"></div>
    <hr>
    <p class="full-width">
        <span class="buttons" style="float:left">
            <input type="button" name="cancel" class="close btn btn-danger"  value="Cancel">
        </span>
        <span class="buttons" style="float:right">
            <input type="submit" value="Continue" class="btn btn-success">
        </span>
     </p>
</form>
</div>
<div id="new-user-form" style="display:<?php echo $user ? 'none' :'block'; ?>;">
<form method="post" class="user" action="#users/lookup/form">
    <table class="table">
    <?php
        if(!$form) $form = UserForm::getInstance();
        $form->render(true, 'Create New User'); ?>
    </table>
    <hr>
    <p class="full-width">
        <span class="buttons" style="float:left">
            <input class="btn btn-warning" type="reset" value="Reset">
            <input type="button" name="cancel" class="<?php echo $user ? 'cancel' : 'close' ?> btn btn-danger"  value="Cancel">
        </span>
        <span class="buttons" style="float:right">
            <input class="btn btn-success" type="submit" value="Add User">
        </span>
     </p>
</form>
</div>
<div class="clear"></div>
</div>
<script type="text/javascript">
$(function() {
    $('#user-search').typeahead({
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
            $('#the-lookup-form').load(
                "ajax.php/users/select/"+obj.id
            );
        },
        property: "/bin/true"
    });

    $('a#unselect-user').click( function(e) {
        e.preventDefault();
        $('div#selected-user-info').hide();
        $('div#new-user-form').fadeIn();
        return false;
     });

    $(document).on('click', 'form.user input.cancel', function (e) {
        e.preventDefault();
        $('div#new-user-form').hide();
        $('div#selected-user-info').fadeIn();
        return false;
     });
});
</script>
