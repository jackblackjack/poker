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
    
    public $chart = array(
        11=>'Hight card', 20=>'Pair', 22=>'Two pairs', 30=>'Three of a kind', 31.1=>'Straight', 
        31.2=>'Flush', 32=>'Full house', 40=>'Four of a kind', 50=>'Straight flush'
    );
    
    public $cardValues = array(
        2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10, 
        11=>'Jack', 12=>'Queen', 13=>'King', 14=>'Ace'   
    );
    
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
        $decision = Brain::model(array('player'=>$this))->result;
        if($this->round->name != 'showDown') $this->round->bank += end($decision);
        $command = key($decision);
        $this->$command(end($decision));
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
    
    public function show($handValue)
    {
        $this->moveName = 'show';
        $combinationValue = substr($handValue, 0, 7)*1;
        $handHeight = substr($handValue, 8, 2)*1; 
        $this->handValue = $combinationValue . ', ' . $this->cardValues[$handHeight] . ' high';
        $this->live = 0;
    }
    
    public static function model($params=false) 
    {
        return new self($params);
    }
}
