<?php
class Strategy extends ActiveRecord
{
    const SQLTABLE = "strategy";
    
    public static $sqlFields = array(
        "id"=>"int(11) auto_increment primary key",
        "player_id"=>"int(11) not null",
    );
    public $round;
    public $player;
    
    public function getResult()
    {
        return array('move'=>'bet', 'amount'=>10);
    }
    
    public function getRoundHistory()
    {
        return Move::model()->findAllByAttribtes(array('round_id'=>$this->round->id));
    }
        
    public function getGameHistory()
    {
        return Move::model()->findAllByAttribtes(array('game_id'=>$this->round->game_id));
    }
    
    public static function model($className = __CLASS__) 
    {
        return new $className;
    }
}