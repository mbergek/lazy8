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


class SourceMessageController extends CController
{
	

	/**
	 * @var string specifies the default action to be 'list'.
	 */
	public $defaultAction='admin';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;
	private $editinglanguage;
	public $errors;
	public $hasErrors;
	public function getErrors()
	{
		return $this->errors!=null?$this->errors:array();
	}

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', 
				'actions'=>array('admin','createlang','importlang','createlangitem'),
				'expression'=>'Yii::app()->user->getState(\'allowAdmin\')',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Create a language
	 * 
	 */
	public function actionCreatelangitem()
	{
		$model=new SourceMessage;
		if(isset($_POST['SourceMessage']))
		{
			$model->attributes=$_POST['SourceMessage'];
			$maxId=0;
			$maxIdSearch=SourceMessage::model()->find(
				array('select'=>'MAX(id) as id,category,message'));
			if($maxIdSearch!==null)
				$maxId=	$maxIdSearch->id + 1;
			$model->id=$maxId;
			$englishTrans=$_POST['englishTrans'];
			if($model->save()){
				//now we must add to all of the languages.
				$langs=Message::model()->findAll(array('select'=>'distinct language'));
				if(isset($langs) && count($langs)>0){
					foreach($langs as $lang){
						$newLangItem=new Message();
						$newLangItem->id=$model->id;
						$newLangItem->language=$lang->language;
						$newLangItem->translation=$englishTrans;
						$newLangItem->save();
					}
					$this->editinglanguage=$langs[0]->language;
				}
				$this->redirect(array('admin','id'=>$model->id));
			}
		}
		$model->category="lazy8";
		$this->render('create',array('model'=>$model));
	}
	/**
	 * Create a language
	 * 
	 */
	public function actionCreatelang()
	{
		$model=new Message();
		if(isset($_POST['Message']))
		{
			$model->attributes=$_POST['Message'];
			if(strlen($model->language)!=0){
				$model->id=1;
				$uniqueTest=Message::model()->find(array('condition'=>'language=\''.$model->language.'\''));
				//echo $uniqueTest->language.";".count($uniqueTest).";";print_r($uniqueTest);die();
				if($uniqueTest===null){
					$sources=SourceMessage::model()->findAll();
					foreach($sources as $source){
						$trans=new Message();
						$trans->id=$source->id;
						$trans->translation=/*'-dirty-'.*/$source->message;
						$trans->language=$model->language;
						$trans->save();
					}
					$this->redirect(array('admin','language'=>$model->language));
				}else{
					$model->addError('language',Yii::t('lazy8','This language already exists. Enter another language'));
				}
			}else{
				$model->addError('language',Yii::t('lazy8','You must enter a language'));
			}
		}
		
		$this->render('createlang',array('model'=>$model));
	}
	private static function sqlCleanString($stringToClean)
	{
		// replace '  "   \   with \'  \"  \\
		return str_replace(array("\\","'","\""),array("\\\\","\\"."'","\\\""),$stringToClean);
	}
	private static function getNodeText($nodeReport,$elementName){
		$nodeElements = $nodeReport->getElementsByTagName($elementName);
		foreach($nodeElements as $nodeElement){
			return $nodeElement->nodeValue;
		}
		return "";
	}
	private static function appendTextNode($dom,$parent,$name,$text){
		$a=$dom->createTextNode($text);
		$b=$dom->createElement($name);
		$b->appendChild($a);
		$parent->appendChild($b);
	}
	/**
	 * Import a language
	 * 
	 */
	public static function importLanguage($filename,$showMessage=true,$fastImport=false)
	{
		$localErrors=array();
		$allAccounts=array();
		$dom = new domDocument();
		if( ! $dom->load($filename) ){
			if($showMessage)
				throw new CException(Yii::t('lazy8','input file could not be xml parsed'));
			else
				throw new CException('input file could not be xml parsed');
		}
		
		$root = $dom->documentElement;
		if($root->nodeName!="lazy8webportlang"){
			if($showMessage)
				$localErrors=array(array(Yii::t('lazy8','Upload failed.  This is not a valid file.'),Yii::t('lazy8','Select a file and try again')));
			$this->render('importlang');
			return $localErrors;
		}
		if($root->getAttribute('version')>1.00){
			if($showMessage)
				$localErrors=array(array(Yii::t('lazy8','There maybe problems because this is a file version greater then this programs version'),Yii::t('lazy8','Select a file and try again')));
		}	
		$nodeLanguages = $root->getElementsByTagName('language');
		unset($root);
		unset($dom);
		foreach($nodeLanguages as $nodeLanguage){
			//make sure the company code is unique
			//Message::model()->dbConnection->createCommand("DELETE FROM Message WHERE language='".$nodeLanguage->getAttribute('langcode')."'")->execute();
			//make sure that all the source messages exist for this language
			if(!$fastImport){
				$sources=SourceMessage::model()->findAll();
				foreach($sources as $source){
					$foundMessage=Message::model()->find(array('condition'=>"language='".$nodeLanguage->getAttribute('langcode')."' AND id=" . $source->id ));
					if($foundMessage==null){
						$trans=new Message();
						$trans->id=$source->id;
						$trans->translation=/*'-dirty-'.*/$source->message;
						$trans->language=$nodeLanguage->getAttribute('langcode');
						$trans->save();
					}
				}
			}else{
				//quickly delete all occurances of this language
				SourceMessage::model()->dbConnection->createCommand("DELETE FROM Message WHERE language='{$nodeLanguage->getAttribute('langcode')}'")->execute();
			}
			//update the version information		
			if($nodeLanguage->getAttribute('version')!=null){
				$foundOption=Options::model()->find('name=:name AND userId=:id AND companyId=:compid',
					array(':name'=>"Language.Version.".$nodeLanguage->getAttribute('langcode'),':id'=>0,':compid'=>0));
				if($foundOption!==null){
					$foundOption->delete();
				}
				$createOption=new Options();
				$createOption->name="Language.Version.".$nodeLanguage->getAttribute('langcode');
				$createOption->userId=0;
				$createOption->companyId=0;
				$createOption->datavalue=$nodeLanguage->getAttribute('version');
				$createOption->save();
			}
			//get all the messages
			$nodeMessages = $nodeLanguage->getElementsByTagName('message');
			foreach($nodeMessages as $nodeMessage){
				$sources=SourceMessage::model()->find(array('condition'=>"category='".$nodeMessage->getAttribute('category')."' AND message='" . SourceMessageController::sqlCleanString(CHtml::encode($nodeMessage->getAttribute('key'))) . "'"));
				if($sources==null){
					$newSource=new SourceMessage();
					$newSource->message=CHtml::encode($nodeMessage->getAttribute('key'));
					$newSource->category=$nodeMessage->getAttribute('category');
					$maxId=0;
					$command=SourceMessage::model()->dbConnection->createCommand("SELECT MAX(id) as maxid FROM SourceMessage");
					try{
						$reader=$command->query();
					}catch(Exception $e){
						echo '<h2>Died on Sql Maxid</h2>' ;throw $e;
					}
					if($reader!==null && count($reader)>0){
						foreach($reader as $row){
							$maxId=	$row['maxid'] + 1;
							break;//there is only one row here anyway.
						}
						
					}
					$newSource->id=$maxId;
					if($newSource->save()){
						$modelMessage=new Message();
						$modelMessage->language=$nodeLanguage->getAttribute('langcode');
						$modelMessage->id=$newSource->id;
						$modelMessage->translation=SourceMessageController::getNodeText($nodeMessage,"translation");
						$modelMessage->save();
						if(!$fastImport){
							//now we must add to all of the other languages.
							$langs=Message::model()->findAll(array('select'=>'distinct language'));
							if(isset($langs) && count($langs)>0){
								foreach($langs as $lang){
									if($nodeLanguage->getAttribute('langcode')!=$lang->language){
										$newLangItem=new Message();
										$newLangItem->id=$newSource->id;
										$newLangItem->language=$lang->language;
										$newLangItem->translation=CHtml::encode($nodeMessage->getAttribute('key'));
										$newLangItem->save();
									}
								}
							}
						}
					}
				}else{
					//make sure the key is unique
					if(!$fastImport)
						Message::model()->dbConnection->createCommand("DELETE FROM Message WHERE language='".$nodeLanguage->getAttribute('langcode')."' AND id=".$sources->id)->execute();
					$modelMessage=new Message();
					$modelMessage->language=$nodeLanguage->getAttribute('langcode');
					$modelMessage->id=$sources->id;
					$modelMessage->translation=SourceMessageController::getNodeText($nodeMessage,"translation");
					//try{
					if(!$modelMessage->save()){
						if($showMessage)
							$localErrors[]=array(Yii::t('lazy8','Failed import of translation.') . ' = ' .$nodeLanguage->getAttribute('langcode').' ; '.$nodeMessage->getAttribute('key'),Yii::t('lazy8','Select a file and try again'));
					}
					//}catch(Exception $e){
					//	echo "error on langcode=" . $nodeLanguage->getAttribute('langcode') . " key=" . $nodeMessage->getAttribute('key');
					//	die();
					//}
				}
			}
		}
		return $localErrors;
	}
	
	/**
	 * Import a language
	 * 
	 */
	public function actionImportlang()
	{
		if(isset($_POST['importnow']) || isset($_FILES['importfile']))
		{
			$this->hasErrors=false;
			$this->errors=array(array());
			if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'../..'.'/assets/uploadlang.sql'))
				unlink(dirname(__FILE__).DIRECTORY_SEPARATOR.'../..'.'/assets/uploadlang.sql');
			if($_FILES['importfile']['error']<>0){
				if($_FILES['importfile']['error']==4)
					$this->errors=array(array(Yii::t('lazy8','Returned error = 4 which means no file given'),Yii::t('lazy8','Select a file and try again')));
				else
					$this->errors=array(array(Yii::t('lazy8','Returned error') . ' = '. $_FILES['importfile']['error'],Yii::t('lazy8','Select a file and try again')));
			}else{
				$importFile=CUploadedFile::getInstanceByName('importfile');
				$importFile->saveAs(dirname(__FILE__).DIRECTORY_SEPARATOR.'../..'.'/assets/uploadlang.sql');
				$this->errors=$this->importLanguage(dirname(__FILE__).DIRECTORY_SEPARATOR.'../..'.'/assets/uploadlang.sql');
				$this->hasErrors=count($this->errors)>0;
				if (!$this->hasErrors)$this->errors=array(array());
			}
		}else if(isset($_GET['importing'])){
			$this->errors=array(array(Yii::t('lazy8','Upload failed.  Possibly the file was too big.'),Yii::t('lazy8','Select a file and try again')));
		}else{
			$this->hasErrors=false;
			$this->errors=array(array());
		}
		$this->render('importlang');
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$this->processAdminCommand();
		if(isset($_POST['Export']))return;//we just sent a file.  Send more and we mess up the file.

		$criteria=new CDbCriteria;
		$this->editinglanguage="";
		if(isset($_POST['langcode']))$this->editinglanguage=$_POST['langcode'];
		if(strlen($this->editinglanguage)==0){
			$langs=Message::model()->findAll(array('select'=>'distinct language'));
			if(isset($langs) && count($langs)>0){
				$this->editinglanguage=$langs[0]->language;
			}else{
				$this->editinglanguage="en_US";
			}
		}
		$criteria->addSearchCondition('language',$this->editinglanguage);

		$pages=new CPagination(Message::model()->count($criteria));
		$pages->pageSize=Yii::app()->user->getState('NumberRecordsPerPage');
		$pages->applyLimit($criteria);

		$sort=new CSort('Message');
		$sort->applyOrder($criteria);

		$models=Message::model()->findAll($criteria);
		$_GET['langcode']=$this->editinglanguage;
		$version="";
		$foundOption=Options::model()->find('name=:name AND userId=:id AND companyId=:compid',
			array(':name'=>"Language.Version.".$_GET['langcode'],':id'=>0,':compid'=>0));
		if($foundOption!==null){
			$version=$foundOption->datavalue;
		}

		$this->render('admin',array(
			'models'=>$models,
			'pages'=>$pages,
			'sort'=>$sort,
			'language'=>$this->editinglanguage,
			'version'=>$version,
		));
	}

	/**
	 * Executes any command triggered on the admin page.
	 */
	protected function processAdminCommand()
	{
		//print_r($_POST);die();
		if(isset($_POST['Save'])){
			//check that ALL translations are in this translation..
			$sources=SourceMessage::model()->findAll();
			foreach($sources as $source){
				$existstest=Message::model()->find(
					array('condition'=>'language=\''.$_POST['langcode'].
					'\' AND id=' . $source->id ));
				if($existstest===null){
					$trans=new Message();
					$trans->id=$source->id;
					$trans->translation='-dirty-'.$source->message;
					$trans->language=$model->language;
					$trans->save();
				}else{
					if(isset($_POST['Message'][$source->id])){
						
					}
				}
			}
			//do the actual reading of data from the form
			$messages=Message::model()->findAll(array('condition'=>'language=\''.$_POST['langcode'].'\''));
			foreach($messages as $message){
				if(isset($_POST['Message'][$message->id])){
					$message->attributes=$_POST['Message'][$message->id];
					$message->save();
				}
				//delete no longer used posts.
				if(!isset($message->source))
					$message->delete();
			}
		}elseif(isset($_POST['Delete'])){
			SourceMessage::model()->dbConnection->createCommand("DELETE FROM Message WHERE language='".$_POST['langcode']."'")->execute();
			$foundOption=Options::model()->find('name=:name AND userId=:id AND companyId=:compid',
				array(':name'=>"Language.Version.".$_POST['langcode'],':id'=>0,':compid'=>0));
			if($foundOption!==null){
				$foundOption->delete();
			}
			unset($_POST['langcode']);
		}elseif(isset($_POST['Export'])){
			$dom=new DomDocument();
			$dom->encoding='utf-8';
			$root = $dom->createElement("lazy8webportlang");
			$root->setAttribute("version","1.00");
			$language=$dom->createElement("language");
			$language->setAttribute("langcode",$_POST['langcode']);
			$exportLang=Message::model()->with('source')->findAll(
				array('condition'=>"language='".$_POST['langcode']."'",
				      'together'=>true,
				      'order'=>"message",
				));
			foreach($exportLang as $export){
				if(isset($export->source)){
					$message=$dom->createElement("message");
					$message->setAttribute("category",$export->source->category);
					$message->setAttribute("key",html_entity_decode($export->source->message));
					$this->appendTextNode($dom,$message,"translation",$export->translation);
					$language->appendChild($message);
				}
			}
			$root->appendChild($language);
			$dom->appendChild($root);
			$dom->formatOutput = true; 			
			$thefile= $dom->saveXML(); 			
			// set headers
			header("Pragma: no-cache");
			header("Expires: 0");
			header("Content-Description: File Transfer");
			header("Content-Type: text/xml");
			header("Content-Disposition: attachment; filename=\"lazy8webExport.Language.". $_POST['langcode'] . "." . date('Y-m-d_H.i.s') . ".xml\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . strlen($thefile));
			//flush();
						
			print $thefile;
			return;//we may not send any more to the screen or it will mess up the file we just sent!
			
		}elseif(isset($_POST['langcode'])){
			$this->editinglanguage=$_POST['langcode'];
		}
	}
}
