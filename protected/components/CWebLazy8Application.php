<?php
/*
*    This is Lazy8Web, a book-keeping ledger program for professionals
*    Copyright (C) 2010  Thomas Dilts                                 
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or   
*    (at your option) any later version.                                 
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of 
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the  
*    GNU General Public License for more details.                   
*
*    You should have received a copy of the GNU General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
*/

class CWebLazy8Application extends CWebApplication
{
	public $mainMenu;
	
	public function onDeletePost($event)
	{
		$this->raiseEvent('onDeletePost', $event);
	}
	public function onImport($event)
	{
		$this->raiseEvent('onImport', $event);
	}
	public function onExport($event)
	{
		$this->raiseEvent('onExport', $event);
	}
	
	
	/**
	 * Initializes the application.
	 * This method overrides the parent implementation by preloading the 'request' component.
	 */
	protected function init()
	{
		$isBadDatabase=false;
		if(isset($_GET) && isset($_GET['r']) && $_GET['r']=='site/baddatabase')
			$isBadDatabase=true;
		if(!$isBadDatabase && count($_GET)==0||(isset($_GET['r']) && $_GET['r']=='site/index')){
			//just some tests to see if the database is working
			$connection=null;
			try{
				$connection=yii::app()->getDb();
				//new CDbConnection($dsn,$username,$password);
				// establish connection. You may try...catch possible exceptions
				$connection->active=true;
			}catch(Exception $zz){
				$isBadDatabase=true;
			}
			if(!$isBadDatabase){
				try{
					$command=$connection->createCommand('select * from Company LIMIT 1');
					$reader=$command->query();
					
				}catch(Exception $zz){
					//create the database
					$fileread = fopen(dirname(__FILE__).DIRECTORY_SEPARATOR.'../controllers/lazy8web.sql',"r");
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
					yii::app()->getDb()->__sleep(false);
					sleep(3);
					yii::app()->getDb()->setActive(true);
				}
				$criteria=new CDbCriteria;
				$pages=new CPagination(10);
				$pages->pageSize=2;
				$pages->applyLimit($criteria);
				$models=Message::model()->findAll($criteria);
				if(!isset($models) || count($models)==0){
					include('SourceMessageController.php');
					include('ReportController.php');
					//the languages are not initialized yet
					SourceMessageController::importLanguage(dirname(__FILE__).DIRECTORY_SEPARATOR.'../controllers/lazy8weblanguage.xml',false,true);
					//load the reports as well
					ReportController::importFile(dirname(__FILE__).DIRECTORY_SEPARATOR.'../controllers/lazy8webreport.xml',false);
				}
				$models=User::model()->findAll();
				if(!isset($models) || count($models)==0){
					//load an admin person.
					$model=new User;
					$model->username='admin';
					$model->displayname='Admin!ChangeName!';
					$model->salt=hash('sha1', uniqid(rand(), true));
					$model->password=hash('sha1','admin' . $model->salt);
					$model->confirmPassword=hash('sha1','admin' . $model->salt);
					$model->save();
					$optionsUser=User::optionsUserTemplate();
					//preset with the default value for the user options
					foreach($optionsUser as $key=>$option)
						$_POST['option_' . $key]=$option[1];
					foreach($optionsUser as $key=>$option)
						if($option[6]=='true')
							$_POST['option_' . $key]=1;
					User::updateOptionTemplate(User::optionsUserTemplate(),$model->id,0);
				}
			}
		}
		if(!$isBadDatabase){
			$this->mainMenu=array(
			  'Data Entry'=>array(
			      "label"=>Yii::t("lazy8","Data Entry"),
			      "visible"=>Yii::app()->user->getState('allowTrans') || Yii::app()->user->getState('allowAccount') || Yii::app()->user->getState('allowCustomer'),
			       array(
				  "url"=>array("route"=>"trans"),
				  "label"=>Yii::t("lazy8","Transactions"),
				  "visible"=>Yii::app()->user->getState('allowTrans')),
			       array(
				  "url"=>array("route"=>"account"),
				  "label"=>Yii::t("lazy8","Accounts"),
				  "visible"=>Yii::app()->user->getState('allowAccount')),
			       array(
				  "url"=>array("route"=>"customer"),
				  "label"=>Yii::t("lazy8","Customers"),
				  "visible"=>Yii::app()->user->getState('allowCustomer')),
			       ),
			  'Reports'=>array(
			      "label"=>Yii::t("lazy8","Reports"),
			      "visible"=>Yii::app()->user->getState('allowReports'),
			      "url"=>array("route"=>"report/report"),
			       ),
			  'Company'=>array(
			      "label"=>Yii::t("lazy8","Company"),
			       "visible"=>Yii::app()->user->getState('allowCompanyCreation')
					|| Yii::app()->user->getState('allowPeriod')
					|| Yii::app()->user->getState('allowAccountTypes')
					|| Yii::app()->user->getState('allowExport')
					|| Yii::app()->user->getState('allowImport'),
			       array(
				  "url"=>array("route"=>"company"),
				  "label"=>Yii::t("lazy8","Company"),
				  "visible"=>Yii::app()->user->getState('allowCompanyCreation')),
			       array(
				  "url"=>array("route"=>"period"),
				  "label"=>Yii::t("lazy8","Period"),
				  "visible"=>Yii::app()->user->getState('allowPeriod') && Yii::app()->user->getState('selectedCompanyId')!=0),
			       array(
				  "url"=>array("route"=>"accountType"),
				  "label"=>Yii::t("lazy8","Account types"),
				  "visible"=>Yii::app()->user->getState('allowAccountTypes') && Yii::app()->user->getState('selectedCompanyId')!=0),
			       array(
				  "url"=>array("route"=>"company/export&id=" . Yii::app()->user->getState('selectedCompanyId')),
				  "label"=>Yii::t("lazy8","Export this company"),
				  "visible"=> !Yii::app()->user->getState('allowCompanyCreation') && Yii::app()->user->getState('allowCompanyExport')!=0),
			       ),
			  'Setup'=>array(
			      "label"=>Yii::t("lazy8","Setup"),
			       "visible"=>Yii::app()->user->getState('allowSelf')
					|| Yii::app()->user->getState('allowAdmin'),
			       array(
				  "url"=>array("route"=>"user/update&id=".Yii::app()->user->id),
				  "label"=>Yii::t("lazy8","Your profile"),
				  "visible"=>Yii::app()->user->getState('allowSelf') || Yii::app()->user->getState('allowAdmin')),
			       array(
				  "url"=>array("route"=>"options"),
				  "label"=>Yii::t("lazy8","Website"),
				  "visible"=>Yii::app()->user->getState('allowAdmin')),
			       array(
				  "url"=>array("route"=>"user"),
				  "label"=>Yii::t("lazy8","Users"),
				  "visible"=>Yii::app()->user->getState('allowAdmin')),
			       array(
				  "url"=>array("route"=>"report"),
				  "label"=>Yii::t("lazy8","Reports"),
				  "visible"=>Yii::app()->user->getState('allowAdmin')),
			       array(
				  "url"=>array("route"=>"sourceMessage"),
				  "label"=>Yii::t("lazy8","Translations"),
				  "visible"=>Yii::app()->user->getState('allowAdmin')),
			       array(
				  "url"=>array("route"=>"changeLog"),
				  "label"=>Yii::t("lazy8","Logs"),
				  "visible"=>Yii::app()->user->getState('allowChangeLog')),
			       ),
	/*                  array(
			      "label"=>Yii::t("lazy8","Help"),
			       array(
				  "url"=>array("route"=>"/product/create"),
				  "label"=>Yii::t("lazy8","Tutorial")),
			       array(
				  "url"=>array("route"=>"/product/create"),
				  "label"=>Yii::t("lazy8","Documentation")),
			       array(
				  "url"=>array("route"=>"/product/create"),
				  "label"=>Yii::t("lazy8","About")),
			    ),*/
			  'Logout'=>array(
			      "url"=>array("route"=>"/site/logout"),
			      "label"=>Yii::t("lazy8","Logout"),
			    ),
			  );
	
			//Load all the modules
			parent::init();
			$mods=yii::app()->modules;
			foreach($mods as $key=>$mod){
				if(is_file(YiiBase::getPathOfAlias($mod['class']).'.php')){
					$type=Yii::import($mod['class'],true);
					$cc=new $type($key,$this);
					$cc->RegisterEvents($this,$this->mainMenu);
				}
			}
		}
	}

	/**
	 * Runs the application.
	 * This method loads static application components. Derived classes usually overrides this
	 * method to do more application-specific tasks.
	 * Remember to call the parent implementation so that static application components are loaded.
	 */
	public function run()
	{
		parent::run();
	}
	
}
class Lazy8Event extends CEvent{
	public $objectUsed;
	public $id;

	function __construct($objectUsed,$id){
		$this->objectUsed=$objectUsed;
		$this->id=$id;
	}
}
