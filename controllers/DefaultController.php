<?php  
class DefaultController extends Controller
{
	public function actionIndex()
	{
        $session = new Session;
        $round = $session->run();
        $this->render('index', array('round'=>$round));

	}
	
	public function actionView()
	{
		
	}
    
    public function actionSql()
    {
        $sql =array('Game','Round','Move');
        foreach ($sql as $s){
            $m = new $s;
            $m->dropSqlTable();
            $m->createSqlTable();
        }
    }
    
    public function actionDelete()
    {
        if(!isset($_GET['id'])) return;
        $game = Game::model()->findAllByAttributes(array('session_id'=>$_GET['id']));
        if($game){
            $game[0]->delete();
            echo "Game and its children are deleted";
        }
    }
    
    public function actionPlayers()
    {
       $a=array(
            array('name'=>'Paul', 'stack'=>500),
            array('name'=>'Alex', 'stack'=>500),
            array('name'=>'Steve', 'stack'=>500),
            array('name'=>'Nick', 'stack'=>500),        
       );
       
       foreach ($a as $player){
           Player::model($player)->save();
       }
    }
}
