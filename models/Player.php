<?php  
class Player extends Common
{
    public $live = TRUE;
    
    public function bet()
    {
        
    }
    
    public static function model($className = __CLASS__) 
    {
        return new $className;
    }
}
