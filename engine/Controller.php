<?php  
class Controller extends Common
{
	public $layout = 'view'; 
	
	public function getAction()
	{
		$url = App::model()->url;
		return (isset($url[2]) && $url[2])
			? $url[2] : 'index';
	}
	
	public function run()
	{
		$action = 'action' . ucfirst($this->action); 
		try{
			if(!method_exists($this, $action))
				throw new Exception('error');	
			call_user_func(array($this, $action));
		}catch(Exception $e){
			echo $e->getMessage();
		}
	}
	
	public function render($view, $options = false)
	{
        if($options) extract($options);  
        require App::model()->getPath($this->layout.'.'.$view) .'.php';
	}
}
