<?php  

require_once "Common.php";

class App extends Common
{
	public $engine = array(
		'engine.Controller',
		'engine.ActiveRecord',
	);
	
	public function getConfig()
	{
		return $this->import('config.main');
	}
	
	public function init()
	{
		foreach($this->config as $key=>$value){
			$this->$key = $value;
		}
		
		foreach($this->engine as $path){
			$this->import($path);
		}
		
		if($this->import){
			foreach($this->import as $path){
				$this->import($path);
			}
		}
        
        $this->controller->run();
	}

	public function getUrl()
	{
		return explode('/', $_SERVER['REQUEST_URI']);
	}

	public function getController()
	{
		$path = $this->url;
		$controllerName = (isset($path[1]) && $path[1]) 
			? ucfirst($path[1]) . 'Controller' 
			: 'DefaultController';
		self::import('controllers.'.$controllerName);
		return new $controllerName;
	}
	
	public static function getPath($path)
	{
		$s = DIRECTORY_SEPARATOR;
		$path = str_replace('.', $s, $path);
		return getenv('DOCUMENT_ROOT') . $s . $path;
	}
    
    public function import($path)
    {
        $s = DIRECTORY_SEPARATOR;
        $path = self::getPath($path);
        $folders = explode($s, $path);
        $file = array_pop($folders);
        $folders = implode($s, $folders);
        
        if($dir = opendir($folders)){
            if($file === '*'){
                while(($file = readdir($dir)) !== false){
                    $fullPath = $folders.$s.$file;
                    if(file_exists($fullPath) && strlen($file)>3){
                        require $fullPath;
                    }
                }
            }else if(file_exists($path.'.php')){
                return include($path.'.php');
            }
        };
    }
	
    public static function model($className = __CLASS__) 
    {
        return new $className;
    }
}
