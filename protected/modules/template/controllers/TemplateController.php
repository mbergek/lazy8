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

class TemplateController extends Controller
{
	/**
	 * @var string specifies the default action to be 'list'.
	 */
	public $defaultAction='admin';
	public $errors;
	public $hasErrors;
	public function getErrors()
	{
		return $this->errors!=null?$this->errors:array();
	}

	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

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
				'actions'=>array('create','update','admin','delete','import','exportall','exportone','reload','editaccounts'),
				'expression'=>'Yii::app()->user->getState(\'allowAdmin\')',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionImport()
	{
		if(isset($_POST['importnow']) || isset($_FILES['importfile']))
		{
			$this->hasErrors=false;
			$this->errors=array(array());
			$filePath=dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../../assets/upload.sql';
			if(file_exists($filePath))
				unlink($filePath);
			if($_FILES['importfile']['error']<>0){
				$this->hasErrors=true;
				if($_FILES['importfile']['error']==4)
					$this->errors=array(array(Yii::t('lazy8','Returned error = 4 which means no file given'),Yii::t('lazy8','Select a file and try again')));
				else
					$this->errors=array(array(Yii::t('lazy8','Returned error') . ' = '. $_FILES['importfile']['error'],Yii::t('lazy8','Select a file and try again')));
			}else{
				$importFile=CUploadedFile::getInstanceByName('importfile');
				$importFile->saveAs($filePath);
				$this->importTemplates($filePath);
			}
		}else if(isset($_GET['importing'])){
			$this->hasErrors=true;
			$this->errors=array(array(Yii::t('lazy8','Upload failed.  Possibly the file was too big.'),Yii::t('lazy8','Select a file and try again')));
		}else{
			$this->hasErrors=false;
			$this->errors=array(array());
		}
		$this->render('showimport');
	}
	private function importTemplates($fileNameAndPath)
	{
		$allAccounts=array();
		$dom = new domDocument();
		if( ! $dom->load($fileNameAndPath) ){
			throw new CException(Yii::t('lazy8','input file could not be xml parsed'));
		}
		
		$root = $dom->documentElement;
		if($root->nodeName!="lazy8webtemplates"){
			$this->hasErrors=true;
			$this->errors=array(array(Yii::t('lazy8','Upload failed.  This is not a valid file.'),Yii::t('lazy8','Select a file and try again')));
			$this->render('showimport');
			return 0;
		}
		if($root->getAttribute('version')>1.00)
			$this->errors=array(array(Yii::t('lazy8','There maybe problems because this is a file version greater then this programs version'),Yii::t('lazy8','Select a file and try again')));
		Template::importTemplates($root,Yii::app()->user->getState('selectedCompanyId'),$this->errors);
	}
	public function actionExportone($id)
	{
		// set headers
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Content-Description: File Transfer");
		header("Content-Type: text/xml");
		header("Content-Disposition: attachment; filename=\"lazy8webExport.Templates.". 
			CompanyController::replace_bad_filename_chars(Yii::app()->user->getState('selectedCompanyName')) . "." . date('Y-m-d_H.i.s') . ".xml\"");
		header("Content-Transfer-Encoding: binary");
		$writer = new XMLWriter();
	
		$writer->openURI('php://output');
		$writer->startDocument('1.0','utf-8');
		
		$writer->setIndent(4);
		$writer->startElement('lazy8webtemplates');
		$writer->writeAttribute('version', '1.00');
		
		Template::exportTemplate($writer,$id);
		$writer->endElement();
		$writer->endDocument();
		
		$writer->flush();
	}
	public function actionExportall()
	{
		// set headers
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Content-Description: File Transfer");
		header("Content-Type: text/xml");
		header("Content-Disposition: attachment; filename=\"lazy8webExport.Templates.". 
			CompanyController::replace_bad_filename_chars(Yii::app()->user->getState('selectedCompanyName')) . "." . date('Y-m-d_H.i.s') . ".xml\"");
		header("Content-Transfer-Encoding: binary");
		$writer = new XMLWriter();
	
		$writer->openURI('php://output');
		$writer->startDocument('1.0','utf-8');
		
		$writer->setIndent(4);
		$writer->startElement('lazy8webtemplates');
		$writer->writeAttribute('version', '1.00');
		
		Template::exportAllTemplates($writer,Yii::app()->user->getState('selectedCompanyId'));
		$writer->endElement();
		$writer->endDocument();
		
		$writer->flush();
	}
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Template;
		$model->companyId=Yii::app()->user->getState('selectedCompanyId');
		$model->allowFreeTextField=1;
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Template']))
		{
			$model->attributes=$_POST['Template'];
			$model->companyId=Yii::app()->user->getState('selectedCompanyId');
			if($model->save()){
				if($this->updateRows($model)){
					if(isset($_POST['AddRow'])||isset($_POST['deleterow'])){
						$this->redirect(array('update','id'=>$model->id));
					}else{
						$this->redirect(array('admin'));
					}
				}
			}
		}

		$model->dateChanged=User::getDateFormatted(date('Y-m-d'));
		
		$this->render('create',array(
			'model'=>$model,'update'=>false,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Template']))
		{
			$model->attributes=$_POST['Template'];
			if($model->save()){
				if($this->updateRows($model)){
					if(isset($_POST['AddRow'])||isset($_POST['deleterow'])){
						$this->redirect(array('update','id'=>$model->id));
					}else{
						$this->redirect(array('admin'));
					}
				}
			}
		}
		$model->dateChanged=User::getDateFormatted(date('Y-m-d'));

		$this->render('update',array(
			'model'=>$model,'update'=>true,
		));
	}

	/**
	 * 
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionEditAccounts($id,$companyId)
	{
		$model=$this->loadRowModel($id);
		if(isset($_POST['Add']) && isset($_POST['accountsfrom']) && count($_POST['accountsfrom'])>0)
		{
			$accounts=$_POST['accountsfrom'];
			foreach($accounts as $account){
				$addRow=new TemplateRowAccount();
				$addRow->templateRowId=$id;
				$addRow->accountId=$account;
				$addRow->save();
			}
		}
		elseif(isset($_POST['Remove']) && isset($_POST['accountsto']) && count($_POST['accountsto'])>0)
		{
			$accounts=$_POST['accountsto'];
			foreach($accounts as $account){
				$accountDelete=TemplateRowAccount::model()->findByPk((int)$account);
				//echo (int)$_POST['accountsto']; die(); 
				if($accountDelete!==null){
					$accountDelete->delete();
				}
			}
		}
		elseif(isset($_POST['Return']))
		{
			$this->redirect(array('update','id'=>$model->templateId));
		}

		$this->render('editaccounts',array(
			'model'=>$model,'companyId'=>$companyId,
		));
	}
	

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		if(isset($_POST['command'], $_POST['id']) && $_POST['command']==='delete')
		{
			$model=$this->loadModel($_POST['id']);
			$model->delete();
			// reload the current page to avoid duplicated delete actions
			$this->refresh();
		}else{
			$criteria=new CDbCriteria;
			$criteria->addSearchCondition('companyId',Yii::app()->user->getState('selectedCompanyId'));
			$criteria->order='sortOrder';
			
			//have had problems here with this crashing directly after initiating the database. If there 
			//is a problem then I just reload the meta data.
			$templateModel=Template::model();
			if($templateModel->getMetaData()==null)
				$templateModel->refreshMetaData();
			$pages=new CPagination($templateModel->count($criteria));
			$pages->pageSize=Yii::app()->user->getState('NumberRecordsPerPage');
			$pages->applyLimit($criteria);
	
			$sort=new CSort('Template');
			$sort->applyOrder($criteria);
	
			$models=$templateModel->findAll($criteria);
		
			$this->render('admin',array(
				'models'=>$models,'pages'=>$pages,'sort'=>$sort,
			));
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Template::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	public function loadRowModel($id)
	{
		$model=TemplateRow::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	private function updateRows(&$model){
		$valid=true;
		if(isset($_POST['save'])&&count($model->templateRows )<2){
			$model->addError('defaultAccountId',Yii::t('lazy8','You must have at least 2 rows in a template.'));
			$valid=false;
		}
		if($model->templateRows!==null && count($model->templateRows)>0){
			//first time through just see if the data makes sense
			$repParams=$model->templateRows;
			$numBalances=0;
			$multiLineRows=0;

			foreach($repParams as $n=>$repParam){
				//see if there is new data to be added..
				if(isset($_POST['save'])){
					$accounts=TemplateRowAccount::model()->findAll(array('condition'=>'templateRowId='.$repParam->id));
					if($accounts==null||count($accounts)==0){
						$model->addError('defaultAccountId','#'.($n+1).'--'.Yii::t('lazy8','You must have at least one account for each row.'));
						$valid=false;
					}
				}
				if($_POST['TemplateRow'][$n]){
					$repParam->attributes=$_POST['TemplateRow'][$n];
				}
				if($repParam->isFinalBalance)$numBalances++;
				if($repParam->allowRepeatThisRow)$multiLineRows++;
				if($repParam->isFinalBalance){
					//all others should be zero
					if(strlen($repParam->defaultValue)>0){
						$model->addError('[0]defaultValue','#'.($n+1).'--'.Yii::t('lazy8','Balance rows cannot have a default value.'));
						$valid=false;
					}
					if($repParam->allowMinus){
						$model->addError('[0]allowMinus','#'.($n+1).'--'.Yii::t('lazy8','Balance rows cannot have a minus value'));
						$valid=false;
					}
					if(strlen($repParam->phpFieldCalc)>0){
						$model->addError('[0]phpFieldCalc','#'.($n+1).'--'.Yii::t('lazy8','Balance rows cannot also be a php calculated field'));
						$valid=false;
					}
					if($repParam->allowChangeValue){
						$model->addError('[0]allowChangeValue','#'.($n+1).'--'.Yii::t('lazy8','Balance rows cannot allow to change value manually'));
						$valid=false;
					}
					if($repParam->allowRepeatThisRow){
						$model->addError('[0]allowRepeatThisRow','#'.($n+1).'--'.Yii::t('lazy8','Balance rows cannot be a repeatable row'));
						$valid=false;
					}
				}
				if(strlen($repParam->phpFieldCalc)>0){
					//most others should be zero
					if(strlen($repParam->defaultValue)>0){
						$model->addError('[0]defaultValue','#'.($n+1).'--'.Yii::t('lazy8','Php calculated rows cannot have a default value.'));
						$valid=false;
					}
					if($repParam->allowMinus){
						$model->addError('[0]allowMinus','#'.($n+1).'--'.Yii::t('lazy8','Php calculated rows cannot have a minus value'));
						$valid=false;
					}
					if($repParam->allowChangeValue){
						$model->addError('[0]allowChangeValue','#'.($n+1).'--'.Yii::t('lazy8','Php calculated rows cannot allow to change value manually'));
						$valid=false;
					}
					if($repParam->allowRepeatThisRow){
						$model->addError('[0]allowRepeatThisRow','#'.($n+1).'--'.Yii::t('lazy8','Php calculated rows cannot be a repeatable row'));
						$valid=false;
					}
				}
				if($repParam->allowMinus && !$repParam->allowChangeValue){
					$model->addError('[0]allowChangeValue','#'.($n+1).'--'.Yii::t('lazy8','Allow minus values and not allowing changing values does not make sense.'));
					$valid=false;
				}
			}
			if($numBalances>1){
				$model->addError('[0]isFinalBalance',Yii::t('lazy8','Only one row is allowed to be a balance row.')); 
				$valid=false;
			}
			if($multiLineRows>1){
				$model->addError('[0]allowRepeatThisRow',Yii::t('lazy8','Only one row is allowed to be a repeatable row.')); 
				$valid=false;
			}
			if($valid){
				//now save it all
				foreach($repParams as $n=>$repParam){
					if(!$repParam->save())
						return false;
				}
			}else	
				return $valid;
		}
		if(isset($_POST['AddRow'])){
			$rowparam=new TemplateRow();
			$rowparam->templateId=$model->id;
			$rowparam->allowChangeValue=1;
			$rowparam->save();
		}
		if(isset($_POST['editrow'])){
			$edits=$_POST['editrow'];
			//there is only one item in this array, but I don't know 
			//any other way to get at it without doing this..
			foreach($edits as $key=>$transrow){
				$this->redirect(array('editaccounts','id'=>$model->templateRows[$key]->id,'companyId'=>$model->companyId));
				break;
			}
		}
		if(isset($_POST['deleterow'])){
			$deletes=$_POST['deleterow'];
			//there is only one item in this array, but I don't know 
			//any other way to get at it without doing this..
			foreach($deletes as $key=>$transrow){
				$model->templateRows[$key]->delete();
				break;
			}
		}
	return $valid;
	}
}
