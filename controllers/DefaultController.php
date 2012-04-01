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
}
