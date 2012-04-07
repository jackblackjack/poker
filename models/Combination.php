<?php
class Combination extends Common
{
    public $cards;
    public $suits = array();
    public $values = array();
    private $handValue;
    
    public static $cardValues = array(
        2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10, 
        'Jack'=>11, 'Queen'=>12, 'King'=>13, 'Ace'=>14   
    );

    public function getHandValue(){
        $this->adoptCards();
        $this->getDuplicates();
        $this->getFlush();
        $this->getStraight();
        return $this->combinaionValue*1000000000000000 + $this->combinationHeight*10000000000 + $this->handHeight;
    }

    
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

    public function getDuplicates()
    {
        if($this->combinaionValue > 40) return;
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
    
    public function getFlush()
    {
        $array = array_count_values($this->suits);
        asort($array);
        $values = array();
        foreach($this->cards as $card){
            if($card['suit'] == end(array_keys($array))){
                $values[] = self::$cardValues[$card['value']];
            }
        }
        arsort($values);
        if(end($array) > 4){
            if($this->getStraight($values)) $this->combinaionValue = 50;
            else{
                $this->combinaionValue = 31.2;
                $values = array_slice($values, 0, 5);
                $this->combinationHeight = $this->getHandHeight($values);
            }
        }
        return $values;
    }
    
    public function getStraight($values=false)
    {
        if($this->combinaionValue > 31.1) return;
        $array = $values ?  array_unique($values) : array_unique($this->values);
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
        return substr($result,0,10)*1;
    }    
    
    public static function model($params=false) 
    {
        return new self($params);
    }
}