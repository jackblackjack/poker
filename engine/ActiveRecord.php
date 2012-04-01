<?php  
class ActiveRecord extends Common
{
    const SQLTABLE = "active_record";
    const CHILDREN = false;
    const PARENT = false;
    public $id = false;
        
    public function save()
    {
        if(!$this->beforeSave()) return false;
        if($this->id){
            $this->update($this->id);
        }else{
            $this->insert();
        }
        $model = $this->findAllByAttributes(array(), array('order'=>'id DESC'));
        $this->id = $model[0]->id;
        
        $this->afterSave();
        
    }

    public function delete()
    {
        if(!$this->beforeDelete()) return false;
        $id = $this->id;
        $table = $this::SQLTABLE;
        $sql = "DELETE FROM $table where id = $id";
        if(!mysql_query($sql, $this->dbConnection)){
            echo "wrong query:" . $sql; 
            return false; 
        };
        $this->deleteChildren();
        $this->afterDelete();
        return true;
    }
    
    public function deleteChildren()
    {
        $childrenClasses = $this::CHILDREN;
        if(!$childrenClasses) return "Child class is not defined";
        $childrenClasses = explode(',', $childrenClasses);
        foreach($childrenClasses as $childClass){
            $pid = strtolower($this->className).'_id';
            $children = $childClass::model()->findAllByAttributes(array($pid=>$this->id));
            foreach($children as $child){
                $child->delete();
            }            
        }
    }
    
    public function findByPk($id)
    {
        $table = $this::SQLTABLE;
        $sql = "SELECT * FROM $table where id = $id";
        $sqlResult = mysql_query($sql, $this->dbConnection);
        $result = $this->sqlToModels($sqlResult);
        return $result ? $result[0] : false;
    }
    
    public function findAllByAttributes($attrs=array(), $params=array())
    {
        $table = $this::SQLTABLE;
        //SELECT
        $select = 'SELECT *';
        if(isset($params['select'])){
            'SELECT '.$params['select'];
            unset($params['select']);
        };
        //CONDITIONS
        $conditions = array();
        if($attrs){
           foreach($attrs as $field=>$value){
                array_push($conditions, " $field = '$value' "); 
            }
           $conditions = implode('AND', $conditions);
        }
        if(isset($params['where'])){
            $conditions .= $params['where'];
            unset($params['where']); 
        }
        $conditions = $conditions ? ' WHERE ' . $conditions : '';
        //PARAMS
        $paramTitles = array('group'=>' GROUP BY ', 'order'=> ' ORDER BY ', 'limit'=>' LIMIT ');
        $sql = "$select from $table $conditions";
        if($params){
            foreach($paramTitles as $key=>$value){
                if(isset($params[$key])) $sql .= $value.$params[$key];
            }
        }
        $sqlResult = mysql_query($sql, $this->dbConnection);
        if(!$sqlResult) echo $sql;
        
        return $this->sqlToModels($sqlResult);
    }
    
    public function insert()
    {
        $table = $this::SQLTABLE;
        $fields = implode(',', array_keys($this::$sqlFields));
        $values = implode(',', $this->sqlValues);
        $sql = "insert into $table ($fields) values($values)";
        if(!mysql_query($sql, $this->dbConnection)) echo "wrong query:" . $sql;
    }
    
    public function update($id)
    {
        $table = $this::SQLTABLE;
        $query = array();
        foreach($this->sqlValues as $field=>$value){
            array_push($query, $field.'='.$value);
        }
        $query = implode(',', $query);
        $sql = "update $table set $query where id = $id";
        if(!mysql_query($sql, $this->dbConnection)) echo "wrong query:" . $sql;
    }
    
    public function getDbConnection()
    {
        $config = App::model()->config['db'];
        $link = mysql_connect($config['host'], $config['user'], $config['password']);
        //if(!mysql_query("create database if not exists " . App::model()->config['db']['database'], $link)) echo 2; exit;
        mysql_select_db($config['database'], $link);
        return $link;
    }
    
    public function disconnect($link)
    {
        mysql_close($link);
    }
    
    public function createSqlTable()
    {
        $table = $this::SQLTABLE;
        $fields = array();
        foreach($this::$sqlFields as $key=>$value){
            array_push($fields, $key . ' ' . $value);
        }
        $sql = "create table if not exists $table (" . implode(', ', $fields) . ")";
        if(!mysql_query($sql, $this->dbConnection)) echo "wrong query:" . $sql;
    }

    public function dropSqlTable()
    {
        $table = $this::SQLTABLE;
        $sql = "DROP TABLE $table";
        if(!mysql_query($sql, $this->dbConnection)) echo "wrong query:" . $sql;
    }
    
    public function getSqlValues()
    {
        $fields = array_keys($this::$sqlFields);
        $result = array();
        
        foreach($fields as $field){
            $value = is_numeric($this->$field) ? $this->$field : "'".serialize($this->$field)."'";
            $result[$field] = $value;
        }
        
        return $result;
    }
    
    protected function sqlToModels($sqlResult)
    {
        if(mysql_num_rows($sqlResult) == 0) return false;
        
        $models = array();
        while($row = mysql_fetch_assoc($sqlResult)){
            $model = new $this->className;
            foreach($row as $field=>$value){
                $model->$field = is_numeric($value) ? $value : unserialize($value);
            }
            array_push($models, $model);
        }
        
        return $models;
    }
    
    public function beforeSave()
    {
        return true;
    }

    public function afterSave()
    {
        return true;
    }
        
    
    public function beforeDelete()
    {
        return true;
    }

    public function afterDelete()
    {
        return true;
    }
    
    public function getId()
    {
        if(isset($this->id) && $this->id) return $this->id;
        $className = $this->className; 
        $models = $className::model()->findAllByAttributes();
        return $models ? $models[count($models)-1] : false; 
    }
    
    public function lastChild($className)
    {
        $parentNameId = strtolower($this->className).'_id';
        $item = $className::model()->findAllByAttributes(array($parentNameId=>$this->id), array('order'=>'id DESC', 'limit'=>1));
        return $item ? $item[0] : false;
    }
    
    public static function model($className = __CLASS__) 
    {
        return new $className;
    }
}
