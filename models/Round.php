<?php
class Round extends ActiveRecord
{
    const SQLTABLE = "round";
    const CHILDREN = "Move";
    const PARENT = "Game";
    
    public static $sqlFields = array(
        "id"=>"int(11) auto_increment primary key",
        "name"=>"varchar(255) not null",
        "players"=>"text not null",
        "live"=>"int(1) default 1",
        "game_id"=>"int(11) not null",
        "bank"=>"int(11) not null",
        "board"=>"text not null",
    );
    public $live = 1;
    public $activeSeat = 0;
    public $name = 'preflop';
    public $bank = 0;
    public $amount = 0;
    
    public $chart = array(
        11=>'Hight card', 20=>'Pair', 22=>'Two pairs', 30=>'Three of a kind', 31.1=>'Straight', 
        31.2=>'Flush', 32=>'Full house', 40=>'Four of a kind', 50=>'Straight flush'
    );
    
    public function run()
    {
        if(!$this->id) $this->save();
        if(!$this->live) return $this->parent->run();

        $lastMove = $this->lastChild;
        if($lastMove){
            $this->activeSeat = $this->nextActiveSeat($lastMove->seat);
            $this->bank += $lastMove->amount;
            $this->amount = $lastMove->amount;
        }
        $activePlayer = $this->players[$this->activeSeat];
        if($this->amount > 0 && $activePlayer->amount == $this->amount){
            $this->closeRound();
            $this->save();
            return $this->parent->run();
        }else{
            $this->players[$this->activeSeat]->move($this);
            $this->save();
        }
        return $this;
    }
    
    public function showDown()
    {
        $winner = Combination::model()->comparePlayers($this->board, $this->players);
        $this->players[$winner->seat]->stack += $this->bank;
    }
    
    public function nextActiveSeat($seat=0)
    {
        $players = $this->players;
        if(!$players) return false;
        
        for ($i = $seat+1; $i < count($players); $i++){
            if(isset($players[$i]) && $players[$i]->live){
                return $i;
            }
        }
        return min(array_keys($players));
    }
    
    public function closeRound()
    {
        if(count($this->players) == 1){
            $player = end($this->players);
            $player->stack += $this->bank;
            $this->endGame();
        }else if($this->name == 'river'){
            $this->showDown();
            $this->endGame();
        }
        
        foreach($this->players as $player){
            $player->amount = 0;
        }
        $this->live = 0;
    }    
    
    public function endGame()
    {
        $parent = $this->parent;
        $parent->live = 0;
        $parent->save();
    }
    
    public static function model($params=false) 
    {
        return new self($params);
    }
}