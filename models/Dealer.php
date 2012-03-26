<?php  
class Dealer extends Common
{
    private $deck = FALSE;
    private $flop = FALSE;
    private $turn = FALSE;
    private $river = FALSE;
    public $table = array();
    
    public static $cardSuits = array(
        'h'=>'Hearts', 'd'=>'Diamonds', 'c'=>'Clubs', 's'=>'Spades'
    );
    
    public static $cardValues = array(
        2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10, 
        'Jack'=>11, 'Queen'=>12, 'King'=>13, 'Ace'=>14   
    );
    
    public function getDeck()
    {
        if($his->deck) 
            return $this->deck;
        
        $this->deck = array();
        foreach(self::$cardSuits as $suit){
            foreach(self::$cardValues as $value=>$trash){
                array_push($this->deck, array('suit'=>$suit, 'value'=>$value));
            }
        }
        shuffle($this->deck);
        return $this->deck;
    }
    
    public static function model($className = __CLASS__) 
    {
        return new $className;
    }
    
    public function deal($players)
    {
        foreach($players as $player){
            $player->cards = array(array_shift($this->deck), array_pop($this->deck));
        }
    }
    
    public function flop()
    {
        if($this->flop)
            return $this->flop;
        
        $this->flop = $this->table = array(array_shift($this->deck), array_shift($this->deck), array_shift($this->deck)); 
        return $this->flop;
    }
    
    public function turn()
    {
        if($this->turn)
            return $this->turn;
        
        $this->turn = array_shift($this->deck);
        array_push($this->table, $this->turn); 
        return $this->turn;
    }
    
    public function river()
    {
        if($this->river)
            return $this->river;
        
        $this->river = array_shift($this->deck);
        array_push($this->table, $this->river); 
        return $this->river;
    }
}
