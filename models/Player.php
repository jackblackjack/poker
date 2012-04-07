<?php  
class Player extends ActiveRecord
{
    const SQLTABLE = "player";
    const CHILDREN = "Move";

    public static $sqlFields = array(
        "id"=>"int(11) auto_increment primary key",
        "name"=>"varchar(255) not null",
        "stack"=>"int(11) default 0",
    );
   // public $name = 'tester';
    public $live = 1;
    public $cards = array();
    public $stack = 200;
    public $amount = 0;
    public $seat = 0;
    public $strategy;
    public $moveName = "fold";
    public $handValue = 0;

    public function move($round)
    {
        $this->round = $round;
        $this->think();
        Move::model(array(
            "player_id"=>$this->id,
            "seat"=>$this->seat,
            "round_id"=>$round->id,
            "game_id"=>$round->game_id,
            "round_name"=>$round->name,
            "move_name"=>$this->moveName,
            "amount"=>$this->amount,
            "stack"=>$this->stack,
        ))->save();
    }

    public function think()
    {
        if($this->amount == $this->round->amount){
            $this->round->closeRound();
            return;
        }

        $decision = Brain::model(array('round'=>$this->round, 'player'=>$this))->result;
        $this->$decision['move']($decision['amount']);
    }
    
    public function bet($amount)
    {
        $this->moveName = 'bet';
        if($this->stack+$this->amount <= $amount) $this->call();
        $this->stack -= $amount-$this->amount;
        $this->amount = $amount;
    }
    
    public function call($amount)
    {
        $this->moveName = 'call';
        if($this->stack >= $amount - $this->amount){
            $this->stack -= $amount;
            $this->amount = $amount;
        }else{
            $this->amount = $this->amount + $this->stack;
            $this->stack = 0;
        }
    }
    
    public function check()
    {
        $this->moveName = 'check';
    }
    
    public function fold()
    {
        $this->moveName = 'fold';
        //$this->round->players[$this->seat]->live = 0;
        $this->live = 0;
    }        
    
    public static function model($params=false) 
    {
        return new self($params);
    }
}
