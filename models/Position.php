<?php
class Position extends Common
{
    public static $cardSuits = array(
        'h'=>'Hearts', 'd'=>'Diamonds', 'c'=>'Clubs', 's'=>'Spades'
    );
    
    public static $cardValues = array(
        2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10, 
        'Jack'=>11, 'Queen'=>12, 'King'=>13, 'Ace'=>14   
    );
    
    public function comparePlayers($table, $players)
    {
        $values = array();
        foreach($players as $key=>$player){
            $values[$key] = $this->combinationValue(array_merge($table, $player->cards));
        }
        asort($values);
        end($values);       
        return $players[key($values)];
    }
    
    public static function model($className = __CLASS__) 
    {
        return new $className;
    }
    
    public function groupFlash()
    {
        
    }
    
    public function groupStraight()
    {
        
    }
    
    public function groupValues()
    {
        
    }
}