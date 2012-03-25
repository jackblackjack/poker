<?php  
class Common
{
    public static $className;
	public function __get($property)
	{
		$method = 'get' . ucfirst($property);
		if(method_exists($this, $method)){
			return call_user_func(array($this, $method));
		}
	}
	
    public static function model($className = __CLASS__) 
    {
        return new $className;
    }
}
