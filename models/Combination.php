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
            $this->suits[] = $card['suit'];
            $this->values[] = $this::$cardValues[$card['value']];
        }
        arsort($this->values);
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
        arsort($duplicates);
        $this->combinaionValue = implode('', array_slice($duplicates, 0, 2))*1;
        $duplicates = array_diff($duplicates, array(1));//leave only duplicates
        switch(count($duplicates)){
            case 0: return false; break;
            case 1: $this->combinaionValue = reset($duplicates)*10; 
                    $this->combinationHeight = key($duplicates); break;
            case 2: $this->combinationHeight = $this->getHandHeight(array_slice(array_keys($duplicates),0, 2)); break;
            case 3: $res = reset($duplicates)==3 ? key($duplicates) : false;
                    $duplicates = array_diff($duplicates, array(3));
                    krsort($duplicates);
                    $res = $res ? array($res, key($duplicates)) : array_slice(array_keys($duplicates),0, 2);
                    $this->combinationHeight = $this->getHandHeight($res); break;
        }
        return true;
    }
    
    public function getFlush($array=false)
    {
        $array = array_count_values($this->suits);
        asort($array);
        
        if(end($array) > 4 && $this->combinaionValue < 32){
            $this->combinaionValue = 31.2;
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
        if($this->combinaionValue > 31.1) return;
        $array = array_unique($this->values);
        if(in_array(14, $array)) array_push($array, 1); //ace as 1 too
        arsort($array);
        $array = array_combine(range((count($array)-1),0), $array);
        for($i=count($array)-1; $i>0; $i--){
            if(isset($array[$i-4]) && $array[$i-4] == $array[$i]-4){
                $this->combinaionValue = 31.1;
                $this->combinationHeight = $array[$i];
                return true;
            } 
        }
        return false;
    }
    
    public function getHandHeight($values=false)
    {
        if($values===false) $values = $this->values;
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