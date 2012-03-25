<?php  
class DefaultController extends Controller
{
	public function actionIndex()
	{
		$model = new Card('h', 'A');
		$this->render('index', array('model'=>$model));
	}
	
	public function actionView()
	{
		echo 1234;
	}
}
