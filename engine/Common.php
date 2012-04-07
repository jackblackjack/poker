<?php  
class Common
{
    public static $className;
    
    public function __construct($params=false)
    {
        if($params && is_array($params)){
            foreach($params as $field=>$value){
                $this->$field = $value;
            }
        }
    }
    
	public function __get($property)
	{
		$method = 'get' . ucfirst($property);
		if(method_exists($this, $method)){
			return call_user_func(array($this, $method));
		}
	}
	
    public static function model($className = __CLASS__, $params = false) 
    {
        return new $className($params);
    }
    
    public function getClassName()
    {
        return  get_class($this);
    }
}
