<?php
class Round extends ActiveRecord
{
    const SQLTABLE = "round";
    const CHILDREN = "Move";
    
    public static $sqlFields = array(
        "id"=>"int(11) auto_increment primary key",
        "name"=>"varchar(255) not null",
        "players"=>"text not null",
        "live"=>"int(1) default 1",
        "game_id"=>"int(11) not null",
  //      "desk"=>"text not null",
        "bank"=>"int(11) not null",
    );
    public $live = 1;
    public $activeSeat = 0;
    public $game;
    public $name = 'preflop';
    public $bank = 0;
    public $amount = 0;
    
    public function run()
    {
        if(!$this->id) $this->save();
        
        $lastMove = $this->lastChild('Move');
        if($this->live && $lastMove){
            $this->activeSeat = $this->nextActiveSeat($lastMove->seat);
            $this->bank += $lastMove->amount;
            $this->amount = $lastMove->amount;
        }
        $activePlayer = $this->players[$this->activeSeat];
        if($activePlayer->amount == $this->amount){
            $this->closeRound();
        }else{
            $this->players[$this->activeSeat]->move($this);
            $this->save();
        }
    }
    
    function preflop()
    {
        
    }
    
    function flop()
    {
        
    }
    
    function turn()
    {
        
    }
    
    function river()
    {
        
    }
    
    public function showDown()
    {
        $winner = Combination::model()->comparePlayers($this->table, $this->players);
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
        $this->save();
    }    
    
    public function endGame()
    {
        $parent = Game::model()->findByPk($this->game_id);
        $parent->live = 0;
        $parent->save();
    }
    
    public static function model($className = __CLASS__) 
    {
        return new $className;
    }
}