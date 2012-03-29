<?php  
class DefaultController extends Controller
{
	public function actionIndex()
	{
		$dealer = new Dealer;
		$this->render('index', array('dealer'=>$dealer));
	}
	
	public function actionView()
	{
		
	}
}
