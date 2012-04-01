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
        if($this->round->name == 'preflop'){
            switch($player->seat){
                case 0: return array('move'=>'bet', 'amount'=>$this->round->game->SB); 
                    break;
                case 1: return array('move'=>'bet', 'amount'=>$this->round->game->BB); 
                    break;
            }
        }
        return array('move'=>'bet', 'amount'=>15);
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