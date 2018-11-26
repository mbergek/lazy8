<?php

class TemplateModule extends CWebModule
{
	public static $templatesVersion="Templates 01.02 2011-02-27"; 
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application
		// import the module-level models and components
		$this->setImport(array(
			'template.models.*',
			'template.components.*',
		));
		//throw new Exception('stop');
	}
	public function RegisterEvents($app,&$mainMenu){
		User::$Version[]=TemplateModule::$templatesVersion;
		$app->onDeletePost=array(new TemplateEventHandler,'eventDelete');
		$app->onImport=array(new TemplateEventHandler,'eventImport');
		$app->onExport=array(new TemplateEventHandler,'eventExport');
		$existsTemplates=false;
		try{$existsTemplates=(Template::model()->find()!=null);}catch(Exception $zz){}
		$mainMenu['Data Entry'][]=array(
			"url"=>array("route"=>"template/useTemplate/selecttemplate"),
			"label"=>Yii::t("lazy8","Templates"),
			"visible"=>Yii::app()->user->getState('allowTrans') && Yii::app()->user->getState('selectedCompanyId')!=0 && $existsTemplates);
		$mainMenu['Setup'][]=array(
			"url"=>array("route"=>"template/template"),
			"label"=>Yii::t("lazy8","Templates"),
			"visible"=>Yii::app()->user->getState('allowAdmin') && Yii::app()->user->getState('selectedCompanyId')!=0);
	}
	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			//make sure this module has been installed
			try{
				$connection=yii::app()->getDb();
				$command=$connection->createCommand('select * from Template LIMIT 1');
				$reader=$command->query();
				
			}catch(Exception $zz){
				//it has not been installed. Install database now
				//create the database
				$fileread = fopen(dirname(__FILE__).DIRECTORY_SEPARATOR.'template.sql',"r");
				while (!feof($fileread)) {
					$sqlCommand="";
					$line=trim(fgets($fileread));
					$sqlCommand.=$line;
					while (!feof($fileread) && substr($line,strlen($line)-1,1) != ";") {
						$line=fgets($fileread);
						$sqlCommand.=$line;
					}
					$command=$connection->createCommand($sqlCommand);
					$command->execute();
				}  
				fclose($fileread);
				//need to give the database time to reset itself. Otherwise it cant find the tables.
				yii::app()->getDb()->__sleep();
				
				sleep(2);
				yii::app()->getDb()->setActive(true);
				//install languages
				//include('SourceMessageController.php');
				//include('ReportController.php');
				//the languages are not initialized yet
				//SourceMessageController::importLanguage(dirname(__FILE__).DIRECTORY_SEPARATOR.'templatetranslations.xml',false,false);
				//load the reports as well
				//ReportController::importFile(dirname(__FILE__).DIRECTORY_SEPARATOR.'templatereport.xml',false);
				
				//must redirect to re-load everything
				//$controller->redirect(array('/'));
			}
			return true;
		}
		else
			return false;
	}
}
class TemplateEventHandler{
	public function eventDelete($event){
		switch($event->objectUsed){
			case 'Account';
				Template::model()->dbConnection->createCommand("DELETE FROM TemplateRowAccount WHERE accountId={$event->id}")->execute();
			break;
			case 'Company';
				$templateList=Template::model()->findAll('companyId=:companyId', array(':companyId'=> $event->id));
				if($templateList!=null){
					foreach($templateList as $model){
						$model->delete();
					}
				}
			break;
		}
	}
	public function eventImport(&$event){
		switch($event->objectUsed['importobject']){
			case 'Company';
				Template::importTemplates($event->objectUsed['root'],$event->id,$event->objectUsed['errors']);
			break;
		}
	}
	public function eventExport($event){
		switch($event->objectUsed['exportobject']){
			case 'Company';
				Template::exportAllTemplates($event->objectUsed['writer'],$event->id);
			break;
		}
	}
}
