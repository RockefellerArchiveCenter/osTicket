<?php
/*********************************************************************
    class.collection.php

    Collection helper

    Hillel Arnold
    Rockefeller Archive Center

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/

class Collection {
    var $id;

    var $coll;

    var $parent;
    var $page;
    var $form;

    function Collection($id) {
        $this->id=0;
        $this->load($id);
    }

    function load($id=0) {

        if(!$id && !($id=$this->getId()))
            return false;

        $sql='SELECT coll.* '
            .', IF(coll.collection_pid IS NULL, coll.collection, CONCAT_WS(" / ", coll2.collection, coll.collection)) as name '
            .' FROM '.COLLECTION_TABLE.' coll '
            .' LEFT JOIN '.COLLECTION_TABLE.' coll2 ON(coll2.collection_id=coll.collection_pid) '
            .' WHERE coll.collection_id='.db_input($id);

        if(!($res=db_query($sql)) || !db_num_rows($res))
            return false;

        $this->coll = db_fetch_array($res);
        $this->id = $this->coll['collection_id'];

        $this->page = $this->form = null;


        return true;
    }

    function reload() {
        return $this->load();
    }

    function asVar() {
        return $this->getName();
    }

    function getId() {
        return $this->id;
    }

    function getPid() {
        return $this->coll['collection_pid'];
    }

    function getParent() {
        if(!$this->parent && $this->getPid())
            $this->parent = self::lookup($this->getPid());

        return $this->parent;
    }

    function getName() {
        return $this->coll['name'];
    }

    function getDeptId() {
        return $this->coll['dept_id'];
    }

    function getSLAId() {
        return $this->coll['sla_id'];
    }

    function getPriorityId() {
        return $this->coll['priority_id'];
    }

    function getStaffId() {
        return $this->coll['staff_id'];
    }

    function getTeamId() {
        return $this->coll['team_id'];
    }

    function getPageId() {
        return $this->coll['page_id'];
    }

    function getPage() {
        if(!$this->page && $this->getPageId())
            $this->page = Page::lookup($this->getPageId());

        return $this->page;
    }

    function getFormId() {
        return $this->coll['form_id'];
    }

    function getForm() {
        if(!$this->form && $this->getFormId())
            $this->form = DynamicForm::lookup($this->getFormId());

        return $this->form;
    }

    function autoRespond() {
        return (!$this->coll['noautoresp']);
    }

    function isEnabled() {
         return ($this->coll['isactive']);
    }

    function isActive() {
        return $this->isEnabled();
    }

    function isPublic() {
        return ($this->coll['ispublic']);
    }

    function getHashtable() {
        return $this->coll;
    }

    function getInfo() {
        return $this->getHashtable();
    }

    function update($vars, &$errors) {

        if(!$this->save($this->getId(), $vars, $errors))
            return false;

        $this->reload();
        return true;
    }

    function delete() {
        $sql='DELETE FROM '.COLLECTION_TABLE.' WHERE collection_id='.db_input($this->getId()).' LIMIT 1';
        if(db_query($sql) && ($num=db_affected_rows())) {
            db_query('UPDATE '.COLLECTION_TABLE.' SET collection_pid=0 WHERE collection_pid='.db_input($this->getId()));
            db_query('UPDATE '.TICKET_TABLE.' SET collection_id=0 WHERE collection_id='.db_input($this->getId()));
            db_query('DELETE FROM '.FAQ_COLLECTION_TABLE.' WHERE collection_id='.db_input($this->getId()));
        }

        return $num;
    }
    /*** Static functions ***/
    function create($vars, &$errors) {
        return self::save(0, $vars, $errors);
    }

    function getCollections($publicOnly=false) {
        $collections=array();
        $sql='SELECT coll.collection_id, CONCAT_WS(" / ", coll2.collection, coll.collection) as name '
            .' FROM '.COLLECTION_TABLE. ' coll '
            .' LEFT JOIN '.COLLECTION_TABLE.' coll2 ON(coll2.collection_id=coll.collection_pid) '
            .' WHERE coll.isactive=1';

        if($publicOnly)
            $sql.=' AND coll.ispublic=1';

        $sql.=' ORDER BY name';
        if(($res=db_query($sql)) && db_num_rows($res))
            while(list($id, $name)=db_fetch_row($res))
                $collections[$id]=$name;

        return $collections;
    }

    function getPublicCollections() {
        return self::getCollections(true);
    }

    function getIdByName($name, $pid=0) {
        $sql='SELECT collection_id FROM '.COLLECTION_TABLE
            .' WHERE collection='.db_input($name)
            .' AND collection_pid='.db_input($pid);
        if(($res=db_query($sql)) && db_num_rows($res))
            list($id) = db_fetch_row($res);

        return $id;
    }

    function lookup($id) {
        return ($id && is_numeric($id) && ($t= new Collection($id)) && $t->getId()==$id)?$t:null;
    }

    function save($id, $vars, &$errors) {

        $vars['collection']=Format::striptags(trim($vars['collection']));

        if($id && $id!=$vars['id'])
            $errors['err']='Internal error. Try again';

        if(!$vars['collection'])
            $errors['collection']='Collection name required';
        elseif(strlen($vars['collection'])<5)
            $errors['collection']='Collection name is too short. 5 chars minimum';
        elseif(($tid=self::getIdByName($vars['collection'], $vars['pid'])) && $tid!=$id)
            $errors['collection']='Collection already exists';

        if(!$vars['dept_id'])
            $errors['dept_id']='You must select a department';

        if($errors) return false;

        foreach (array('sla_id','form_id','page_id','pid') as $f)
            if (!isset($vars[$f]))
                $vars[$f] = 0;
        $sql=' updated=NOW() '
            .',collection='.db_input($vars['collection'])
            .',collection_pid='.db_input($vars['pid'])
            .',dept_id='.db_input($vars['dept_id'])
            .',priority_id='.db_input(isset($vars['priority_id'])
                ? $vars['priority_id'] : 0)
            .',sla_id='.db_input($vars['sla_id'])
            .',form_id='.db_input($vars['form_id'])
            .',page_id='.db_input($vars['page_id'])
            .',isactive='.db_input($vars['isactive'])
            .',ispublic='.db_input($vars['ispublic'])
            .',noautoresp='.db_input(isset($vars['noautoresp']) && $vars['noautoresp']?1:0)
            .',notes='.db_input(Format::sanitize($vars['notes']))
            .',color='.db_input($vars['color']);

        //Auto assign ID is overloaded...
        if($vars['assign'] && $vars['assign'][0]=='s')
             $sql.=',team_id=0, staff_id='.db_input(preg_replace("/[^0-9]/", "", $vars['assign']));
        elseif($vars['assign'] && $vars['assign'][0]=='t')
            $sql.=',staff_id=0, team_id='.db_input(preg_replace("/[^0-9]/", "", $vars['assign']));
        else
            $sql.=',staff_id=0, team_id=0 '; //no auto-assignment!
        if($id) {
            $sql='UPDATE '.COLLECTION_TABLE.' SET '.$sql.' WHERE collection_id='.db_input($id);
            if(db_query($sql))
                return true;

            $errors['err']='Unable to update collection. Internal error occurred';
        } else {
            if (isset($vars['collection_id']))
                $sql .= ', collection_id='.db_input($vars['collection_id']);
            $sql='INSERT INTO '.COLLECTION_TABLE.' SET '.$sql.',created=NOW()';
            if(db_query($sql) && ($id=db_insert_id()))
                return $id;

            $errors['err']='Unable to create the collection. Internal error';
        }

        return false;
    }
}
?>
