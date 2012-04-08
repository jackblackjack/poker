<?php  
class DefaultController extends Controller
{
	public function actionIndex()
	{
        $session = Session::model()->findByPk(35);
        $round = $session->run();
        $this->render('index', array('round'=>$round));

	}
	
	public function actionReplay()
	{
	    $games = Game::model()->findAllByAttributes();
        $rounds = isset($_GET['game_id']) 
            ? Round::model()->findAllByAttributes(array('game_id'=>$_GET['game_id'])) 
            : false;
        $round = isset($_GET['round_id'])
            ? Round::model()->findByPk($_GET['round_id']) 
            : false;
        if($round && $round->name == "showDown")
            $round->winners = Brain::model()->comparePlayers($round->players);
		$this->render('replay', array('games'=>$games, 'rounds'=>$rounds, 'round'=>$round));
	}
    
    public function actionSql()
    {
        if(isset($_POST['reset'])){
            foreach ($_POST['reset'] as $key=>$val){
                $m = new $key;
                $m->dropSqlTable();
                $m->createSqlTable();
            }            
        }

        $items =array('Session', 'Game','Round','Move', 'Player');
        $this->render('reset', array('items'=>$items));
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
