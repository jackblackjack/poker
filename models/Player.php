<?php  
class Player extends ActiveRecord
{
    public $name;
    public $live = TRUE;
    public $cards = array();
    public $stack = 200;
    public $seat = 0;

    
    public function __construct($player)
    {
        foreach($player as $field=>$value){
            $this->$field = $value;
        }
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
            return $amount;
        }else{
            // возвращаем минусовую разницу для разделения банка
            $stack = $this->stack; 
            $this->stack = 0;
            return $stack;
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
