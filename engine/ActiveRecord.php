<?php  
class ActiveRecord extends Common
{
    public $id = false;
    const SQLTABLE = "active_record";
    
    public function save()
    {
        if($this->id){
            $this->update($this->id);
        }else{
            $this->insert();
        }
    }
    
    public function findByPk($id)
    {
        
    }
    
    public function findAllByAttributes($params){
        
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
    
    public function constructSqlTable()
    {
        $fields = array();
        foreach($this::$sqlFields as $key=>$value){
            array_push($fields, $key . ' ' . $value);
        }
        $sql = "create table if not exists $this::SQLTABLE (" . implode(',', $fields) . ")";
        if(!mysql_query($sql, $this->dbConnection)) echo "wrong query:" . $sql;
    }
    
    public function getSqlValues()
    {
        $fields = array_keys($this::$sqlFields);
        $result = array();
        
        foreach($fields as $field){
            $result[$field] = "'".print_r($this->$field, true)."'";
        }
        
        return $result;
    }
}
