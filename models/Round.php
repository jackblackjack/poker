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
        "amount"=>"int(11) default 0",
        "deck"=>"text not null",
    );
    public $live = 1;
    public $activeSeat = 0;
    public $bank = 0;
    public $amount = 0;
    public $board = array(); 
    
    public function run()
    {
        if(!$this->id) $this->save();
        if(!$this->live || !$this->players) return $this->parent->run();
        
        $lastMove = $this->lastChild;
        if($lastMove){
            $this->activeSeat = $this->nextActiveSeat($lastMove->seat);
            $this->bank += $lastMove->amount;
            $this->amount = $lastMove->amount;
        }
        if(!$this->activeSeat) $this->activeSeat = 0;
        $activePlayer = $this->players[$this->activeSeat];
        if($this->amount == $activePlayer->amount && ($this->amount > 0 
            || count($this->children) == count($this->players)))
        {
            $this->closeRound();
            $this->save();
            return $this->parent->run();
        }else{
            $activePlayer->move($this);
            $this->save();
        }
        return $this;
    }
    
    public function showDown()
    {
        $winner = Brain::model()->comparePlayers($this->players);
        $winner->stack += $this->bank;
    }
    
    public function nextActiveSeat($seat=0)
    {
        if(!$this->players) return false;
        //make array starting from active seat
        $players = array_merge(
            array_slice($this->players, $seat),
            array_slice($this->players, 0,$seat)
        );
        for($i=1; $i<count($players); $i++){
            if($players[$i]->live) return $players[$i]->seat;
        }
    }
    
    public function closeRound()
    {
        if(count($this->players) == 1){
            $player = end($this->players);
            $player->stack += $this->bank;
            $this->endGame();
        }else if($this->name == 'showDown'){
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

    public function getName()
    {
        switch(count($this->parent->children)){
            case 1: return 'preflop'; break;
            case 2: return 'flop'; break;
            case 3: return 'turn'; break;
            case 4: return 'river'; break;
            case 5: return 'showDown'; break;
        }
    }
    
    public static function model($params=false) 
    {
        return new self($params);
    }
}