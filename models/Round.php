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
    public $winners = array();
    
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
            $activePlayer->round = ''; //do not save parent
            if($this->name == 'showDown') 
                $this->winners = Brain::model()->comparePlayers($this->players);
            $this->save();
        }
        return $this;
    }
    
    public function closeRound()
    {
        $players = array_filter($this->players, function($player){
            return $player->live == 1;
        });
        if(count($players) == 1){
            $player = end($players);
            $player->stack += $this->bank;
            $player->save();
            $this->endGame();
        }else if($this->name == 'showDown'){
            $this->winners = Brain::model()->comparePlayers($this->players);
            $this->splitBank();
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
        foreach($this->players as $player){
            $player->unsetAttributes(array(
                'cards', 'handValue', 'amount', 'split', 'live'
            ));
            $player->save();
        }
        $parent = $this->parent;
        $parent->players = $this->players;
        $parent->live = 0;
        $parent->save();
    }
    
    public function splitBank()
    {
        $players = $this->winners;
        foreach($players as $key1=>$player1){
            if($this->bank > 0){
                //нахлдим разделяющих место
                foreach($players as $key2=>$player2){
                    if($player1->absoluetValue == $player2->absoluteValue){
                        $player1->split[$player2->seat] = $player2;
                    }
                }

                if(count($player1->split) > 1){
                    foreach($player1->split as $key3=>$splitter){
                        $prise = $splitter->invest($this) * count($players) * $this->bank / count($player1->split);
                        if($this->bank >= $prise){
                            $splitter->stack += $prise;
                            $this->bank -= $prise;
                        }else{
                            $splitter->stack += $this->bank;
                            $this->bank = 0;
                        }
                        if($player1 !== $splitter){
                            $splitter->live = 0;
                            $plitter->save();
                            unset($players[$key3]);
                        }
                    }
                }else{
                    $prise = $player1->invest($this) * count($players) * $this->bank;
                    if($this->bank >= $prise){
                        $player1->stack += $prise;
                        $this->bank -= $prise;
                    }else{
                        $player1->stack += $this->bank;
                        $this->bank = 0;
                    }
                }                
            }

            $player1->split = array();
            $player1->amount = 0;
            $player1->save();
            $this->players[$player1->seat] = $player1;
            unset($players[$key1]);
        };
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