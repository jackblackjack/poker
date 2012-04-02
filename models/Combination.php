<?php
class Combination extends Common
{
    public $cards;
    public $suits = array();
    public $values = array();
    public $probabiliy = 0;
    private $handValue = array('combinaionValue'=>0, 'combinationHeight'=>0, 'handHeight'=>0);
    
    public $chart = array(
        array(0,0)=>0, array(2,0)=>1, array(2,2)=>2, array(3,0)=>3, 'straight'=>4, 
        'flush'=>5, array(3,2)=>6, array(4,0)=>7, 'straightFlush'=>8
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
        return $this->handValue;
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
        if(end($duplicates) == 1){
            $this->handValue['combinaionValue'] = 0;
            $this->handValue['handHeight'] = $this->handHeight;
            return;
        }
        
        $high = array(end(array_keys($duplicates)) => array_pop($duplicates));
        // ONE DUPLICATE (FROM 2 TO 4 OF A KIND)
        if(!$duplicates || end($duplicates) == 1 || $high[0] == 4){
            $this->handValue['combinaionValue'] = $this->chart[array($high[0], 0)];
            $this->nandValue['combinationHeight'] = key($high);
            $values = array_diff($this->values, $high);
            $this->handValue['handHeight'] = $this->getHandHeight($values);
            return;
        }
        //TWO DUPLICATES (2:2, 2:3)
        $low = array(end(array_keys($duplicates)) => 2);
        $this->handValue['combinaionValue'] = $this->chart[array($high[0], 2)];
        $this->nandValue['combinationHeight'] 
            = $this->getHandHeight(array(end(array_keys($duplicates)), key($high)));
        if($high[0]==2 && count($this->values) > 4){
            $values = array_diff($this->values, $high);
            $values = array_diff($values, $low);
            $this->handValue['handHeight'] = $this->getHandHeight($values);
        }
        
    }
    
    public function getFlush()
    {
        $array = array_count_values($this->suits);
        asort($array);
        if(end($array) == 5&& $this->handValue['combinaionValue'] < 6){
            $this->handValue['combinaionValue'] == 5;
            $this->nandValue['combinationHeight'] = '';////////////
        } 
    }
    
    public function getStraight()
    {
        $array = array_unique($this->values);
        if(count($array) < 5) return;
        if(in_array(14, $array)) array_push(1); //ace as 1 too
        asort($aray);
        ///////////////////        
        $this->nandValue['combinationHeight'] = $this->getHandHeight($array);
    }
    
    public function getHandHeight($values=false)
    {
        if($values===false) $values = $this->values;
        
        usort($values, function($a, $b){
            return $a < $b ? 1 : -1; 
        });
        $result = 0;
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