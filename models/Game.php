<?php
class Game extends ActiveRecord
{
    const SQLTABLE = "game";
    const CHILDREN = "Round";
    
    public static $sqlFields = array(
        "id"=>"int(11) auto_increment primary key",
        "session_id"=>"int(11) not null",
        "live"=>"int(1) default 0",
        "players"=>"text not null",
        "SB"=>"int(11) default 1",
        "BB"=>"int(11) default 2",
        "stack"=>"int(11) default 200",
    );
    public $live = 1;
    public $round;
    public $bank = 0;
    public $deck = array();
    public $table = array();
    
    public static $cardSuits = array(
        'h'=>'Hearts', 'd'=>'Diamonds', 'c'=>'Clubs', 's'=>'Spades'
    );
    
    public static $cardValues = array(
        2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10, 
        'Jack'=>11, 'Queen'=>12, 'King'=>13, 'Ace'=>14   
    );
    
    public function run()
    {
        if(!$this->id) $this->save();
        
        $lastRound = $this->lastChild('Round');
        
        if($lastRound && count($lastRound->players)>1){
            if($lastRound->live){
                $this->round = $lastRound;
                return true;
            }
            $this->deck = $lastRound->deck;
            $this->players = $lastRound->players;
            $this->bank = $lastRound->bank;
            $this->deal();
        }else{
            $this->seatPlayers();
            $this->giveCards();            
        }
        
        $this->round = new Round(array(
            'players'=>$this->players,
            'bank'=>$this->bank,
            'game_id'=>$this->id,
        ));
        $this->round->run();
    }

    public function deal()
    {
        switch(count($this->table)){
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
    

    protected function getDeck()
    {
        if($this->deck) return $this->deck;
        foreach(self::$cardSuits as $suit){
            foreach(self::$cardValues as $value=>$trash){
                array_push($this->deck, array('suit'=>$suit, 'value'=>$value));
            }
        }
        shuffle($this->deck);
        return $this->deck;
    }
}