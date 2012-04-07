<?php
class Move extends ActiveRecord
{
    const SQLTABLE = "move";
    
    public static $sqlFields = array(
        "id"=>"int(11) auto_increment primary key",
        "game_id"=>"int(11) not null",
        "round_id"=>"int(11) not null",
        "round_name"=>"varchar(255) not null",
        "player_id"=>"int(11) not null",
        "seat"=>"int(11) not null",
        "stack"=>"int(11) default 0",
        "move_name"=>"varchar(255) not null",
        "amount"=>"int(11) default 0",
    );
    
    public function afterSave()
    {
        
    }
    
    public static function model($params=false) 
    {
        return new self($params);
    }
}
