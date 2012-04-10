<?php
class Combination extends Common
{
    public $cards;
    public $suits = array();
    public $values = array();
    private $handValue;
    private $absoluteValue;
    
    public static $cardValues = array(
        2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10, 
        'Jack'=>11, 'Queen'=>12, 'King'=>13, 'Ace'=>14   
    );
    
    public static $chart = array(
        110=>'Hight card', 200=>'Pair', 220=>'Two pairs', 300=>'Three of a kind', 311=>'Straight', 
        312=>'Flush', 320=>'Full house', 400=>'Four of a kind', 500=>'Straight flush'
    );
    
    public function getHandValue(){
        $this->adoptCards();
        $this->getDuplicates();
        $this->getFlush();
        $this->getStraight();
        return array(
            'combinationValue'=>array(
                'name'=>$this::$chart[$this->combinaionValue],
                'value'=>$this->combinaionValue
             ), 
            'combinationHeight'=>array(
                'name'=>$this->toNames($this->combinationHeight),
                'value'=>$this->combinationHeight,
             ), 
            'handHeight'=>array(
                'name'=>$this->toNames($this->handHeight),
                'value'=>$this->handHeight,
             ), 
        );
    }

    public function getAbsoluteValue()
    {
        $value = $this->handValue;
        return '0,' . $value['combinationValue']['value'] 
            . $value['combinationHeight']['value'] . $value['handHeight']['value']; 
    }

    public function toNames($value)
    {
        if(!$value) return 0;
        $chart = array_flip($this::$cardValues);
        $result = array();
        for($i=0; $i<strlen($value); $i+=2){
            $result[] = $chart[substr($value, $i, 2)*1];
        }
        return implode(', ', $result);
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
        $this->combinaionValue = implode('', array_slice($duplicates, 0, 2))*10;
        $duplicates = array_diff($duplicates, array(1));//leave only duplicates
        
        switch(count($duplicates)){
            case 0: return false; break;
            case 1: $this->combinaionValue = reset($duplicates)*100; 
                    $this->combinationHeight = $this->getHandHeight(array(key($duplicates))); 
                    $this->handHeight = $this->getHandHeight(
                        array_diff($this->values, array(key($duplicates))), 
                        5-reset($duplicates)
                    );
                    break;
            case 2: $this->combinationHeight = $this->getHandHeight(array_slice(array_keys($duplicates),0, 2));
                    $this->handHeight = $this->getHandHeight(
                        array_diff($this->values, array_slice(array_keys($duplicates),0, 2)),
                        5-(reset($duplicates)+next($duplicates))
                    );
                    break;
            case 3: $res = reset($duplicates)==3 ? key($duplicates) : false;
                    $duplicates1 = array_diff($duplicates, array(3));
                    krsort($duplicates1);
                    $res = $res ? array($res, key($duplicates1)) : array_slice(array_keys($duplicates1),0, 2);
                    $this->combinationHeight = $this->getHandHeight($res); break;
                    $this->handHeight = reset($duplicates)==3 ? 0 : max(array_diff($this->values, $res));
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
            if($this->getStraight($values)) $this->combinaionValue = 500;
            else{
                $this->combinaionValue = 312;
                $values = array_slice($values, 0, 5);
                $this->combinationHeight = $this->getHandHeight = $this->getHandHeight($values);
            }
        }
        return $values;
    }
    
    public function getStraight($values=false)
    {
        if($this->combinaionValue > 311) return;
        $array = $values ?  array_unique($values) : array_unique($this->values);
        if(in_array(14, $array)) array_push($array, 1); //ace as 1 too
        arsort($array);
        $array = array_combine(range((count($array)-1),0), $array);
        for($i=count($array)-1; $i>0; $i--){
            if(isset($array[$i-4]) && $array[$i-4] == $array[$i]-4){
                $this->combinaionValue = 311;
                $this->combinationHeight = $this->handHeight = $this->getHandHeight(array($array[$i]));
                return true;
            } 
        }
        return false;
    }
    
    public function getHandHeight($values=false, $count=5)
    {
        if($count < 1) return 0;
        if($values===false) $values = $this->values;
        $result = '';
        foreach($values as $value){
            $prefix = $value < 10 ? '0' : '';
            $result .= $prefix.$value;
        }
        return substr($result,0,$count*2);
    }    
    
    public static function model($params=false) 
    {
        return new self($params);
    }
}