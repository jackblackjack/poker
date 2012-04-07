<?php
class Session extends ActiveRecord
{
    const SQLTABLE = "session";
    const CHILDREN = "Game";
    
    public static $sqlFields = array(
        "id"=>"int(11) auto_increment primary key",
        "SB"=>"int(11) default 1",
        "BB"=>"int(11) default 2",
        "stack"=>"int(11) default 200",
    );
    public $SB = 1;
    public $BB = 2;
    public $stack = 200;
    
    public function run()
    {
        if(!$this->id) $this->save();

        $lastGame = $this->lastChild;
        if($lastGame){
            if($lastGame->live)
                return $lastGame->run();
            $this->players = $lastGame->players;
        }  

        
        return Game:: model(array(
            'session_id'=>$this->id,
            'players'=>$this->players, 
            'SB'=>$this->SB,
            'BB'=>$this->BB,
            'stack'=>$this->stack
        ))->run();
    }
    
    public function getPlayers()
    {
        if(isset($this->players) && $this->players) 
            return $this->players;
        
        return Player::model()->findAllByAttributes(array(), array('limit'=>4));
    }
    
    public static function model($params=false) 
    {
        return new self($params);
    }
}