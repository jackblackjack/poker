<?php  
class DefaultController extends Controller
{
	public function actionIndex()
	{
		$dealer = new Dealer;
        //$dealer->insert();
		$this->render('index', array('dealer'=>$dealer));
	}
	
	public function actionView()
	{
		
	}
}
