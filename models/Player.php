<?php  
class Player extends Common
{
    public $name;
    public $live = TRUE;
    public $cards = array();
    public $stack = 200;
    public $seat = 0;

    
    public function __construct($player)
    {
        $this->name = $player['name'];
        $this->stack = $player['stack'];
    }
        
    public function bet($amount)
    {
        $this->stack -= $amount;
        return $amount; 
    }
    
    public function call($amount)
    {
        if($this->stack >= $amount){
            $this->stack -= $amount;
            return true;
        }else{
            // возвращаем минусовую разницу для разделения банка
            $stack = $this->stack; 
            $this->stack = 0;
            return $stack - $amount;
        }
    }
    
    public function check()
    {
       return true;
    }
    
    public function fold()
    {
       $this->live = false;
       return false;
    }        
    
    public static function model($className = __CLASS__) 
    {
        return new $className;
    }
}
