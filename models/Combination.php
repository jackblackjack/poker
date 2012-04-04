<?php
class Combination extends Common
{
    public $cards;
    public $suits = array();
    public $values = array();
    public $probabiliy = 0;
    private $handValue;
    
    public $chart = array(
        0=>'Hight card', 20=>'Pair', 22=>'Two pairs', 30=>'Three of a kind', 31=>'Straight', 
        31.5=>'Flush', 32=>'Full house', 40=>'Four of a kind', 50=>'Straight flush'
    );
    
    public static $cardSuits = array(
        'h'=>'Hearts', 'd'=>'Diamonds', 'c'=>'Clubs', 's'=>'Spades'
    );
    
    public static $cardValues = array(
        2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10, 
        'Jack'=>11, 'Queen'=>12, 'King'=>13, 'Ace'=>14   
    );
    
    public function adoptCards()
    {
        $this->suits = array();
        $this->values = array();
        
        foreach($this->cards as $card){
            array_push($this->suits, $card['suit']);
            array_push($this->values, $this::$cardValues[$card['value']]);
        }
        asort($this->values);
    }

    public function getHandValue(){
        $this->adoptCards();
        $this->getDuplicates();
        $this->getFlush();
        $this->getStraight();
        return array($this->combinaionValue, $this->combinationHeight, $this->handHeight);
    }
    
    public function comparePlayers($table, $players)
    {
        $values = array();
        foreach($players as $key=>$player){
            $this->adopt(array_merge($table, $player->cards));
            $values[$key] = $this->combinationValue;
        }
        asort($values);
        end($values);       
        return $players[key($values)];
    }
    
    public function getDuplicates()
    {
        $duplicates = array_count_values($this->values);
        asort($duplicates);
        // NO DUPLICATES
        if(end($duplicates) == 1)return;
        
        $high = array(end(array_keys($duplicates)) => array_pop($duplicates));
        // ONE DUPLICATE (FROM 2 TO 4 OF A KIND)
        if(!$duplicates || end($duplicates) == 1 || end($high) == 4){
            $this->combinaionValue = end($high)*10;
            $this->combinationHeight = key($high);
            return;
        }
        //TWO DUPLICATES (2:2, 2:3)
        $low = array(end(array_keys($duplicates)) => 2);
        $this->combinaionValue = end($high)*10+2;
        $this->combinationHeight = $this->getHandHeight(array(key($low), key($high)));
    }
    
    public function getFlush($array=false)
    {
        $array = array_count_values($this->suits);
        asort($array);
        
        if(end($array) > 4 && $this->combinaionValue < 32){
            $this->combinaionValue = 31.5;
            $values = array();
            foreach($this->cards as $card){
                if($card['suit'] == end(array_keys($array))){
                    $values[] = self::$cardValues[$card['value']];
                }
            }
            asort($values);
            $values = array_slice($values, -5);
            $this->combinationHeight = $this->getHandHeight($values);
            return true;
        }
        return false;
    }
    
    public function getStraight()
    {
        if($this->combinaionValue > 31) return;
        
        $array = array_unique($this->values);
        if(in_array(14, $array)) array_push($array, 1); //ace as 1 too
        if(count($array) < 5) return;
        arsort($array);
        
        $high = 1;
        $card = current($array);
        
        foreach($array as $key=>$value){
            if($value == $card-1 && $high <5){
                if($high == 1) array_unshift($array, $card);
                $high++;
            }else if($high < 5){
                $hight = 0;
                unset($array[$key]);
            }
            $card = $value;
        }

        if($high > 4){
            $this->combinaionValue = 31;
            asort($array);
            $this->combinationHeight = array_pop($array);
            return true;
        }
        return false;
    }
    
    public function getHandHeight($values=false)
    {
        if($values===false) $values = $this->values;
        
        usort($values, function($a, $b){
            return $a < $b ? 1 : -1; 
        });
        $result = '';
        foreach($values as $value){
            $prefix = $value < 10 ? 0 : '';
            $result .= $prefix.$value;
        }
        return $result*1;
    }    
    
    public static function model($className = __CLASS__) 
    {
        return new $className;
    }
}