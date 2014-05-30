<?php
/*********************************************************************
    collections.php

    Collections.

    Hillel Arnold
    Rockefeller Archive Center

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('admin.inc.php');
include_once(INCLUDE_DIR.'class.collection.php');
require_once(INCLUDE_DIR.'class.dynamic_forms.php');

$collection=null;
if($_REQUEST['id'] && !($collection=Collection::lookup($_REQUEST['id'])))
    $errors['err']='Unknown or invalid collection ID.';

if($_POST){
    switch(strtolower($_POST['do'])){
        case 'update':
            if(!$collection){
                $errors['err']='Unknown or invalid collection.';
            }elseif($collection->update($_POST,$errors)){
                $msg='Collection updated successfully';
            }elseif(!$errors['err']){
                $errors['err']='Error updating collection. Try again!';
            }
            break;
        case 'create':
            if(($id=Collection::create($_POST,$errors))){
                $msg='Collection added successfully';
                $_REQUEST['a']=null;
            }elseif(!$errors['err']){
                $errors['err']='Unable to add collection. Correct error(s) below and try again.';
            }
            break;
        case 'mass_process':
            if(!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
                $errors['err'] = 'You must select at least one collection';
            } else {
                $count=count($_POST['ids']);

                switch(strtolower($_POST['a'])) {
                    case 'enable':
                        $sql='UPDATE '.COLLECTION_TABLE.' SET isactive=1 '
                            .' WHERE collection_id IN ('.implode(',', db_input($_POST['ids'])).')';
                    
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Selected collections enabled';
                            else
                                $warn = "$num of $count selected collections enabled";
                        } else {
                            $errors['err'] = 'Unable to enable selected collections.';
                        }
                        break;
                    case 'disable':
                        $sql='UPDATE '.COLLECTION_TABLE.' SET isactive=0 '
                            .' WHERE collection_id IN ('.implode(',', db_input($_POST['ids'])).')';
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Selected collections disabled';
                            else
                                $warn = "$num of $count selected collections disabled";
                        } else {
                            $errors['err'] ='Unable to disable selected collection(s)';
                        }
                        break;
                    case 'delete':
                        $i=0;
                        foreach($_POST['ids'] as $k=>$v) {
                            if(($t=Collection::lookup($v)) && $t->delete())
                                $i++;
                        }

                        if($i && $i==$count)
                            $msg = 'Selected collections deleted successfully';
                        elseif($i>0)
                            $warn = "$i of $count selected collections deleted";
                        elseif(!$errors['err'])
                            $errors['err']  = 'Unable to delete selected collections';

                        break;
                    default:
                        $errors['err']='Unknown action - get technical help.';
                }
            }
            break;
        default:
            $errors['err']='Unknown command/action';
            break;
    }
    if ($id or $collection) {
        if (!$id) $id=$collection->getId();
    }
}

$page='collections.inc.php';
if($collection || ($_REQUEST['a'] && !strcasecmp($_REQUEST['a'],'add')))
    $page='collection.inc.php';

$nav->setTabActive('manage');
require(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer.inc.php');
?>
