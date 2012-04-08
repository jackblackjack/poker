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
   
    public $live = 1;
    public $cards = array();
    public $stack = 200;
    public $amount = 0;
    public $seat = 0;
    public $split = array();
    public $moveName = "fold";
    public $handValue = array(
        'combinationValue'=>array('name'=>'', 'value'=>0), 
        'combinationHeight'=>array('name'=>'', 'value'=>0), 
        'handHeight'=>array('name'=>'', 'value'=>0)
    );
    
    
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
        $decision = Brain::model(array('player'=>$this))->result;
        $command = key($decision);
        $this->$command(end($decision));
    }
    
    public function bet($amount)
    {
        $this->moveName = 'bet';
        if($this->stack <= $amount-$this->amount) $this->call($amount-$this->amount);
        $this->stack -= $amount-$this->amount;
        $this->amount += $amount;
    }
    
    public function call($amount)
    {
        $this->moveName = 'call';
        if($this->stack >= $amount - $this->amount){
            $this->stack -= $amount;
            $this->amount += $amount;
        }else{
            $this->amount += $this->amount + $this->stack;
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
        $this->save();
        $this->live = 0;
    }        
    
    public function show($handValue)
    {
        $this->moveName = 'show';
        $this->handValue = $handValue;
        $this->live = 0;
    }
    
    public static function model($params=false) 
    {
        return new self($params);
    }
}
