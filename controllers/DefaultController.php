<?php  
class DefaultController extends Controller
{
	public function actionIndex()
	{
		$model = Dealer::model()->deck;
		$this->render('index', array('model'=>$model));
	}
	
	public function actionView()
	{
		echo 1234;
	}
}
