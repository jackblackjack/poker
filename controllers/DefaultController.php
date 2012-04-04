<?php  
class DefaultController extends Controller
{
	public function actionIndex()
	{
        //$session = Session::model()->findByPk(9);
        //$session->run();
        //$session->game->delete();
        //$session->run();
        $combination = new Combination(array(
            'cards'=>array(
                array('suit'=>'Diamonds', 'value'=>'Jack'),
                array('suit'=>'Hearts', 'value'=>'Queen'),
                array('suit'=>'Diamonds', 'value'=>'King'),
                array('suit'=>'Hearts', 'value'=>10),
                array('suit'=>'Diamonds', 'value'=>3),
                array('suit'=>'Diamonds', 'value'=>5),
                array('suit'=>'Clubs', 'value'=>'Ace'),
            )
        ));
        print_r($combination->handValue);
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
