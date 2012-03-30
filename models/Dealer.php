<?php  
class Dealer extends ActiveRecord
{
    const SQLTABLE = "dealer";
    public static $sqlFields = array(
        "id"=>"int(11) auto_increment primary key",
        "deck"=>"text not null",
        "bank"=>"int(11) default 0",
        "players"=>"text not null"
    );
    public $deck = array();
    public $table = array();
    public $bank = 0;
    public $smallBlind = 1;
    public $bigBlind = 2;
    public $players = array();
    
    public static $cardSuits = array(
        'h'=>'Hearts', 'd'=>'Diamonds', 'c'=>'Clubs', 's'=>'Spades'
    );
    
    public static $cardValues = array(
        2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10, 
        'Jack'=>11, 'Queen'=>12, 'King'=>13, 'Ace'=>14   
    );
    
    public function __construct()
    {
        $players = array(
            array('name'=>'Steve', 'stack'=>200, 'seat'=>3),
            array('name'=>'Nick', 'stack'=>200, 'seat'=>2),
            array('name'=>'Paul', 'stack'=>200, 'seat'=>1),
            array('name'=>'John', 'stack'=>200, 'seat'=>4),
        );
        foreach($players as $player){
            array_push($this->players, new Player($player));
        };
        $this->newRound();
    }
    
    public function newRound()
    {
        $this->makeDeck();
        $this->seatPlayers();
        $this->giveCards();
    }

    public function deal()
    {
        switch(count($this->desk)){
            case 0:
                $this->showFlop();
                break;
            case 3:
                $this->showTurn();
                break;
            case 4:
                $this->showRiver();
                break;
        }
        return $this->table;
    }

    protected function makeDeck()
    {
        foreach(self::$cardSuits as $suit){
            foreach(self::$cardValues as $value=>$trash){
                array_push($this->deck, array('suit'=>$suit, 'value'=>$value));
            }
        }
        shuffle($this->deck);
    }

    protected function seatPlayers()
    {
        $group = array();
        foreach($this->players as $player){
            if($player->stack > $this->smallBlind){
                array_push($group, $player);
            }
        }
        usort($group, function($a, $b){
            return $a->seat > $b->seat ? 1 : -1;
        });
        
        //$this->players= array();

        foreach($group as $key => $player){
            $player->seat = $key == 0 ? count($group) - 1 : $key - 1;
            $this->players[$player->seat] = $player;
        }
        
    }
            
    protected function giveCards()
    {
        foreach($this->players as $player){
            $player->cards = array(array_shift($this->deck), array_pop($this->deck));
        };        
    }

    protected function showFlop()
    {
        $this->table = array(array_shift($this->deck), array_shift($this->deck), array_shift($this->deck));
    }
    
    protected function showTurn()
    {
        array_push($this->table, array_shift($this->deck)); 
    }
    
    protected function showRiver()
    {
        array_push($this->table, array_shift($this->deck)); 
    }
    
    public static function model($className = __CLASS__) 
    {
        return new $className;
    }
}
