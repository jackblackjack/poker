<?php  
class DefaultController extends Controller
{
	public function actionIndex()
	{
        $session = Session::model()->findByPk(9);
        $session->run();
        //$session->game->delete();
        //$session->run();
	}
	
	public function actionView()
	{
		
	}
    
    public function actionSql()
    {
        $sql =array('Session','Player','Game','Round','Move');
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
}
