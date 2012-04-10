<?php
class Brain extends ActiveRecord
{
    const SQLTABLE = "strategy";
    
    public static $sqlFields = array(
        "id"=>"int(11) auto_increment primary key",
        "player_id"=>"int(11) not null",
    );
    public $player;
    public $handValue;
    public $cards;
    
    public function getResult()
    {
        $roundName = $this->player->round->name;
        $this->cards = array('cards' => array_merge($this->player->round->board, $this->player->cards));
        $this->handValue = Combination::model($this->cards)->handValue; 
        
        return $this->$roundName();
    }
    
    public function preflop()
    {
        switch($this->player->seat){
            case 0: return $this->player->round->bank == 0 
                ? array('bet'=>$this->player->round->parent->parent->SB) 
                : array('call'=>$this->player->round->amount - $this->player->amount);
                break;
            case 1: return $this->player->round->bank == 1 
                ? array('bet'=>$this->player->round->parent->parent->BB) 
                : array('call'=>$this->player->round->amount - $this->player->amount); 
                break;
        }
        if((int)$this->handValue['combinationValue']['value'] > 110 && $this->player->stack > $this->player->round->amount)
            return array('bet'=>round($this->player->round->bank / 1.5));
        else if($this->player->round->amount > 0) return array('call'=>$this->player->round->amount - $this->player->amount);
        else return array('check'=>$this->player->round->amount - $this->player->amount);
    }
    
    public function flop()
    {
        if($this->player->round->amount > 0) return array('call'=>$this->player->round->amount - $this->player->amount);
        else return array('check'=>$this->player->round->amount - $this->player->amount);
    }
    
    public function turn()
    {
        if($this->player->round->amount > 0) return array('call'=>$this->player->round->amount - $this->player->amount);
        else return array('check'=>$this->player->round->amount - $this->player->amount);
    }
    
    
    public function river()
    {
        if($this->player->round->amount > 0) return array('call'=>$this->player->round->amount - $this->player->amount);
        else return array('check'=>$this->player->round->amount - $this->player->amount);
    }

    public function showDown()
    {
        return array('show'=>$this->handValue);
    }
    
    public function comparePlayers($players)
    {
        usort($players, function($a, $b){
           return $a->absoluteValue < $b->absoluteValue;
        }); 
        return $players;
    }
    
    public function getRoundHistory()
    {
        return Move::model()->findAllByAttribtes(array('round_id'=>$this->round->id));
    }
        
    public function getGameHistory()
    {
        return Move::model()->findAllByAttribtes(array('game_id'=>$this->round->game_id));
    }
    
    public static function model($params=false) 
    {
        return new self($params);
    }
}