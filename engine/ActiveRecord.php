<?php  
class ActiveRecord extends Common
{
    public function save()
    {
        
    }
    
    public function connect()
    {
        $config = App::model()->config['db'];
        $link = mysql_connect($config['host'], $config['user'], $config['password']);
        if (!$link) die('Could not connect: ' . mysql_error());
        echo 'Connected successfully';
        mysql_close($link);
    }
}
