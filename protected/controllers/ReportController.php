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


class ReportController extends CController
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
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;

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
				'actions'=>array('create','update','admin','import','exportall','reload'),
				'expression'=>'Yii::app()->user->getState(\'allowAdmin\')',
			),
			array('allow', 
				'actions'=>array('report'),
				'expression'=>'Yii::app()->user->getState(\'allowReports\')',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionCreate()
	{
		$model=new Report();
		if(isset($_POST['Report']))
		{
			$model->attributes=$_POST['Report'];
			//required due to bug in yii
//			$model->phpSecondaryInfo=$_POST['Report']['selectSql'];
			if($model->save()){
				$this->updateParameters($model);
				if(isset($_POST['AddRow'])||isset($_POST['deleterow'])||isset($_POST['AddGroupRow'])||isset($_POST['deletegrouprow'])||isset($_POST['AddGroupFieldRow'])||isset($_POST['deletegroupfieldrow'])||isset($_POST['AddReportRow'])||isset($_POST['deletereportrow'])){
					$this->redirect(array('update','id'=>$model->id));
				}else{
					$this->redirect(array('admin'));
				}
			}
		}
		$this->render('create',array('model'=>$model));
	}
	private function updateParameters($model){
		if($model->reportparameters!==null && count($model->reportparameters)>0){
			$repParams=$model->reportparameters;
			foreach($repParams as $n=>$repParam){
				//see if there is new data to be added..
				if($_POST['ReportParameters'][$n]){
					$repParam->attributes=$_POST['ReportParameters'][$n];
					//this is a bug in yii that requires me to do the next row
					$repParam->defaultValue=$_POST['ReportParameters'][$n]['defaultValue'];
					$repParam->isDefaultPhp=$_POST['ReportParameters'][$n]['isDefaultPhp'];
					$repParam->phpSecondaryInfo=$_POST['ReportParameters'][$n]['phpSecondaryInfo'];
					$repParam->dataType=$_POST['ReportParameters'][$n]['dataType'];
					//echo  $repParam->dataType . ' ' . $_POST['ReportParameters'][$n]['dataType'];
					//die();
					$repParam->save();
				}
			}
		}
		if($model->rows!==null && count($model->rows)>0){
			$repRows=$model->rows;
			foreach($repRows as $n=>$repRow){
				$repRow->attributes=$_POST['ReportRows'][$n];
				$repRow->save();
			}
		}
		if($model->groups!==null && count($model->groups)>0){
			$repGroups=$model->groups;
			foreach($repGroups as $n=>$repGroup){
				//see if there is new data to be added..
				if($_POST['ReportGroups'][$n]){
					$repGroup->attributes=$_POST['ReportGroups'][$n];
					$repGroup->save();
					if($repGroup->fields!==null && count($repGroup->fields)>0){
						$repGroupFields=$repGroup->fields;
						foreach($repGroupFields as $m=>$repGroupField){
							//see if there is new data to be added..
							$repGroupField->sortOrder=$_POST['ReportGroupFields'][$n]['sortOrder'][$m];
							$repGroupField->fieldName=$_POST['ReportGroupFields'][$n]['fieldName'][$m];
							$repGroupField->fieldWidth=$_POST['ReportGroupFields'][$n]['fieldWidth'][$m];
							$repGroupField->row=$_POST['ReportGroupFields'][$n]['row'][$m];
							$repGroupField->isDate=$_POST['ReportGroupFields'][$n]['isDate'][$m];
							$repGroupField->isDecimal=$_POST['ReportGroupFields'][$n]['isDecimal'][$m];
							$repGroupField->fieldCalc=$_POST['ReportGroupFields'][$n]['fieldCalc'][$m];
							$repGroupField->save();
						}
					}
				}
			}
		}
		if(isset($_POST['AddRow'])){
			$rowparam=new ReportParameters();
			$rowparam->reportId=$model->id;
			$rowparam->save();
		}
		if(isset($_POST['deleterow'])){
			$deletes=$_POST['deleterow'];
			//there is only one item in this array, but I don't know 
			//any other way to get at it without doing this..
			foreach($deletes as $key=>$transrow){
				$model->reportparameters[$key]->delete();
				break;
			}
		}
		if(isset($_POST['AddReportRow'])){
			$rowparam=new ReportRows();
			$rowparam->reportId=$model->id;
			$rowparam->save();
		}
		if(isset($_POST['AddGroupRow'])){
			$rowparam=new ReportGroups();
			$rowparam->reportId=$model->id;
			$rowparam->save();
		}
		if(isset($_POST['deletereportrow'])){
			$deletes=$_POST['deletereportrow'];
			//there is only one item in this array, but I don't know 
			//any other way to get at it without doing this..
			foreach($deletes as $key=>$transrow){
				$model->rows[$key]->delete();
				break;
			}
		}
		if(isset($_POST['deletegrouprow'])){
			$deletes=$_POST['deletegrouprow'];
			//there is only one item in this array, but I don't know 
			//any other way to get at it without doing this..
			foreach($deletes as $key=>$transrow){
				$groupFields=$model->groups[$key]->fields;
				$model->groups[$key]->delete();
				break;
			}
		}
		if(isset($_POST['AddGroupFieldRow'])){
			$adds=$_POST['AddGroupFieldRow'];
			//there is only one item in this array, but I don't know 
			//any other way to get at it without doing this..
			foreach($adds as $key=>$transrow){
				$rowGroup=new ReportGroupFields();
				$rowGroup->reportGroupId=$model->groups[$key]->id;
				$rowGroup->save();
				break;
			}
		}
		if(isset($_POST['deletegroupfieldrow'])){
			//print_r($_POST);echo "<br><br><br>";
			//print_r($_POST['deletegroupfieldrow']);
			//die();
			$deletes=$_POST['deletegroupfieldrow'];
			//there is only one item in this array, but I don't know 
			//any other way to get at it without doing this..
			foreach($deletes as $key=>$transrow){
				$deletes2=$_POST['deletegroupfieldrow'][$key];
				foreach($deletes2 as $key2=>$transrow2){
					$model->groups[$key]->fields[$key2]->delete();
					break;
				}
				break;
			}
		}
		if(isset($_POST['Export'])){
			$dom=new DomDocument();
			$dom->encoding='utf-8';
			$root = $dom->createElement("lazy8webportreport");
			$root->setAttribute("version","1.00");
			$this->ExportOneReport($model,$dom,$root);
			$dom->appendChild($root);
			$dom->formatOutput = true; 			
			$thefile= $dom->saveXML(); 			
			// set headers
			include('CompanyController.php');
			header("Pragma: no-cache");
			header("Expires: 0");
			header("Content-Description: File Transfer");
			header("Content-Type: text/xml");
			header("Content-Disposition: attachment; filename=\"lazy8webExport.Report.". CompanyController::replace_bad_filename_chars($model->name) . "." . date('Y-m-d_H.i.s') . ".xml\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . strlen($thefile));
			//flush();
						
			print $thefile;
			
			return;//we may not send any more to the screen or it will mess up the file we just sent!
		}
	}
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionUpdate()
	{
		$model=$this->loadReport();
		if(isset($_POST['Report']))
		{
			$model->attributes=$_POST['Report'];
			//required due to bug in yii
			$model->selectSql=$_POST['Report']['selectSql'];
			if($model->save()){
				$this->updateParameters($model);
				if(isset($_POST['Export']))return;//cant send any more or the export file will be destroyed
				if(isset($_POST['AddRow'])||isset($_POST['deleterow'])||isset($_POST['AddGroupRow'])
					||isset($_POST['deletegrouprow'])||isset($_POST['AddGroupFieldRow'])
					||isset($_POST['deletegroupfieldrow'])||isset($_POST['Export'])
					||isset($_POST['AddReportRow'])||isset($_POST['deletereportrow'])){
					$this->redirect(array('update','id'=>$model->id));
				}else{
					$this->redirect(array('admin'));
				}
			}
		}
		$this->render('update',array('model'=>$model));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'list' page.
	 */
	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadReport()->delete();
			$this->redirect(array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$this->processAdminCommand();

		$criteria=new CDbCriteria;

		$pages=new CPagination(Report::model()->count($criteria));
		$pages->pageSize=Yii::app()->user->getState('NumberRecordsPerPage');
		$pages->applyLimit($criteria);

		$sort=new CSort('Report');
		$sort->applyOrder($criteria);

		$models=Report::model()->findAll($criteria);

		$this->render('admin',array(
			'models'=>$models,
			'pages'=>$pages,
			'sort'=>$sort,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the primary key value. Defaults to null, meaning using the 'id' GET variable
	 */
	public function loadReport($id=null)
	{
		if($this->_model===null)
		{
			if($id!==null || isset($_GET['id']))
				$this->_model=Report::model()->findbyPk($id!==null ? $id : $_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}

	/**
	 * Executes any command triggered on the admin page.
	 */
	protected function processAdminCommand()
	{
		if(isset($_POST['command'], $_POST['id']) && $_POST['command']==='delete')
		{
			$this->loadReport($_POST['id'])->delete();
			// reload the current page to avoid duplicated delete actions
			$this->refresh();
		}
	}


	/**
	 * Displays the requested report
	 * 
	 */
	public function actionReport()
	{
		//throw(new Exception('just die'));
		if(isset($_POST['ShowReport']) && isset($_POST['reportId']))
		{
			$this->_model=Report::model()->findbyPk($_POST['reportId']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
			//delete all the old report parameter values
			ReportUserLastUsedParams::model()->dbConnection->createCommand('DELETE FROM ReportUserLastUsedParams WHERE reportId=' . $this->_model->id . ' AND userId=' . Yii::app()->user->id)->execute();
			//build the Sql sats
			$aliases=array();
			$replacements=array();
			$repParams=$this->_model->reportparameters;
			$parameterValues=array();
			if(isset($repParams)&&count($repParams)>0){
				foreach($repParams as $n=>$repParam){
					//get the new parameter values
					if($repParam->dataType=='HIDDEN_NO_SHOW_HEAD' || $repParam->dataType=='HIDDEN_SHOW_HEAD'){
						if(strlen($repParam->phpSecondaryInfo)>0){
							eval($repParam->phpSecondaryInfo);
						}else{
							$defaultValue=$repParam->defaultValue;
							if($repParam->isDefaultPhp)
								eval($defaultValue);
						}
						$parameterValues[$n]=$defaultValue;
					}else{
						if( $repParam->dataType=='DATE')
							$parameterValues[$n]=User::parseDate($_POST[$n]);
						else
							$parameterValues[$n]=$_POST[$n];
						//save the parameter values
						$saveLast=new ReportUserLastUsedParams();
						$saveLast->reportId=$this->_model->id;
						$saveLast->userId=Yii::app()->user->id;
						$saveLast->paramId=$n;
						$saveLast->LastUsedValue=$parameterValues[$n];
						$saveLast->save();
					}
					//get the aliases
					if(strlen($repParam->alias)>0){
						$aliases[]=$repParam->alias;
						if($repParam->dataType=='HIDDEN_NO_SHOW_HEAD' || $repParam->dataType=='HIDDEN_SHOW_HEAD'){
							$defaultValue=$repParam->defaultValue;
							if($repParam->isDefaultPhp)
								eval($defaultValue);
							$replacements[]=$defaultValue;
						}else{
							if( $repParam->dataType=='DATE')
								$replacements[]=User::parseDate($_POST[$n]);
							else
								$replacements[]=$_POST[$n];
						}
					}
				}
			}
			$sqlSelect=str_replace($aliases,$replacements,$this->_model->selectSql);
			//echo $sqlSelect; die();
			//execute the Sql sats
			$command=Report::model()->dbConnection->createCommand($sqlSelect);
			try{
				$reader=$command->query();
				//foreach($reader as $row)print_r($row);
				//die();
			}catch(Exception $e){
				echo '<h2>Died on Sql execution</h2>'.$sqlSelect ;throw $e;
			}
			//update last used report for printout
			$printoutview=$_POST['printoutview'];
			$option=Options::model()->find('name=\'isReportForPrintout\' AND companyId=0 AND userId='. Yii::app()->user->id);
			$option->datavalue=$printoutview==1?'true':'false';
			$option->save();
			Yii::app()->user->setState('isReportForPrintout',$printoutview);
			//update last used black and white
			$option=Options::model()->find('name=\'isReportBlackAndWhite\' AND companyId=0 AND userId='. Yii::app()->user->id);
			$option->datavalue=$_POST['blackandwhite']==1?'true':'false';
			$option->save();
			Yii::app()->user->setState('isReportBlackAndWhite',$_POST['blackandwhite']);
			if($_POST['blackandwhite']==1)
				Yii::app()->user->setState('reportCssFile',$this->_model->cssBwFileName);
			else
				Yii::app()->user->setState('reportCssFile',$this->_model->cssColorFileName);
			//get the number and date formats
			$cLoc=CLocale::getInstance(Yii::app()->user->getState('languagecode'));
			$numberFormatter=$cLoc->getNumberFormatter();
			$dateFormatter=$cLoc->getDateFormatter();
			$numberFormat=User::getNumberFormat();
			//show the report
			$this->render('report',array('model'=>$this->_model,
					'reader'=>$reader,
					'numberFormatter'=>$numberFormatter,
					'dateFormatter'=>$dateFormatter,
					'numberFormat'=>$numberFormat,
					'printoutview'=>$printoutview,
					'parameterValues'=>$parameterValues));
		}else{
			//show the report parameter selection page.
			$noActivityLogSelectionString="";
			if(!Yii::app()->user->getState('allowChangeLog'))
				$noActivityLogSelectionString=" AND name<>'Activity log'";
			$allRepModels=Report::model()->findAll(array(
				'select'=>'id,name','order'=>'sortOrder',
				'condition'=>'(companyId='.Yii::app()->user->getState('selectedCompanyId') . ' OR companyId=0)' . $noActivityLogSelectionString));
			$reports=CHtml::listData($allRepModels,'id','name');
			foreach($reports as $n=>$reportsTrans){
				$reports[$n]=Yii::t('lazy8',$reportsTrans);
			}
			if($_POST['reportId']){
				$model=Report::model()->findbyPk($_POST['reportId']);
				//update last used report
				$option=Options::model()->find('name=\'lastPrintedReportId\' AND companyId=0 AND userId='. Yii::app()->user->id);
				$option->datavalue=$_POST['reportId'];
				$option->save();
				Yii::app()->user->setState('lastPrintedReportId',$_POST['reportId']);
			}else{
				//always try to select any report
				$selectReport=Yii::app()->user->getState('lastPrintedReportId');
				$model=Report::model()->findbyPk($selectReport);
				if(isset($model)){
					$_POST['reportId']=$selectReport;
				}elseif(isset($allRepModels) && count($allRepModels)>0){
					$_POST['reportId']=$allRepModels[0]->id;
					$model=$allRepModels[0];
				}
				
			}
			//preselect the values to what was done before...
			$repParams=$model->reportparameters;
			if(isset($repParams)&&count($repParams)>0){
				foreach($repParams as $n=>$repParam){
					if($repParam->dataType=='FREE_TEXT' || $repParam->dataType=='DROP_DOWN' || $repParam->dataType=='DATE' || $repParam->dataType=='BOOLEAN'){
						$lastChosen=ReportUserLastUsedParams::model()->find('reportId=' . $model->id . ' AND userId=' . Yii::app()->user->id . ' AND paramId='. $n);
						if(isset($lastChosen)){
							$defaultValue=$lastChosen->LastUsedValue;
						}else{
							$defaultValue=$repParam->defaultValue;
							if($repParam->isDefaultPhp)
								eval($defaultValue);
						}
						if( $repParam->dataType=='DATE' )
							$_POST[$n]=User::getDateFormatted($defaultValue);
						else
							$_POST[$n]=$defaultValue;
					}
				}
			}
			$_POST['blackandwhite']=Yii::app()->user->getState('isReportBlackAndWhite')?1:0;
			$_POST['printoutview']=Yii::app()->user->getState('isReportForPrintout')?1:0;
			$this->render('reportparams',array('reports'=>$reports,'model'=>$model));
		}
		
	}
	public function ExportOneReport($model,&$dom,&$root)
	{
		$report=$dom->createElement("report");
		$report->setAttribute("name",$model->name);
		$this->appendTextNode($dom,$report,"desc",$model->desc);
		$this->appendTextNode($dom,$report,"selectsql",$model->selectSql);
		$this->appendTextNode($dom,$report,"csscolorfilename",$model->cssColorFileName);
		$this->appendTextNode($dom,$report,"cssbwfilename",$model->cssBwFileName);
		$this->appendTextNode($dom,$report,"sortOrder",$model->sortOrder);
		if($model->reportparameters!==null && count($model->reportparameters)>0){
			$repParams=$model->reportparameters;
			foreach($repParams as $repParam){
				$paramNode=$dom->createElement("parameter");
				$paramNode->setAttribute("sortorder",$repParam->sortOrder);
				$paramNode->setAttribute("name",$repParam->name);
				$paramNode->setAttribute("alias",$repParam->alias);
				$paramNode->setAttribute("datatype",$repParam->dataType);
				$paramNode->setAttribute("isdefaultphp",$repParam->isDefaultPhp?'true':'false');
				$paramNode->setAttribute("isdate",$repParam->isDate?'true':'false');
				$paramNode->setAttribute("isdecimal",$repParam->isDecimal?'true':'false');
				$this->appendTextNode($dom,$paramNode,"desc",$repParam->desc);
				$this->appendTextNode($dom,$paramNode,"phpsecondaryinfo",$repParam->phpSecondaryInfo);
				$this->appendTextNode($dom,$paramNode,"defaultvalue",$repParam->defaultValue);
				$report->appendChild($paramNode);
			}
		}
		if($model->groups!==null && count($model->groups)>0){
			$repGroups=$model->groups;
			foreach($repGroups as $repGroup){
				$groupNode=$dom->createElement("group");
				$groupNode->setAttribute("sortorder",$repGroup->sortOrder);
				$groupNode->setAttribute("breakingfield",$repGroup->breakingField);
				$groupNode->setAttribute("pagebreak",$repGroup->pageBreak?'true':'false');
				$groupNode->setAttribute("showgrid",$repGroup->showGrid?'true':'false');
				$groupNode->setAttribute("showheader",$repGroup->showHeader?'true':'false');
				$groupNode->setAttribute("continuesumsovergroup",$repGroup->continueSumsOverGroup?'true':'false');
				if($repGroup->fields!==null && count($repGroup->fields)>0){
					$repGroupFields=$repGroup->fields;
					foreach($repGroupFields as $m=>$repGroupField){
						$groupFieldNode=$dom->createElement("field");
						$groupFieldNode->setAttribute("sortorder",$repGroupField->sortOrder);
						$groupFieldNode->setAttribute("fieldname",$repGroupField->fieldName);
						$groupFieldNode->setAttribute("fieldwidth",$repGroupField->fieldWidth);
						$groupFieldNode->setAttribute("row",$repGroupField->row);
						$groupFieldNode->setAttribute("isdate",$repGroupField->isDate?'true':'false');
						$groupFieldNode->setAttribute("isdecimal",$repGroupField->isDecimal?'true':'false');
						$this->appendTextNode($dom,$groupFieldNode,"fieldcalc",$repGroupField->fieldCalc);
						$groupNode->appendChild($groupFieldNode);
					}
				}
				$report->appendChild($groupNode);
			}
		}
		if($model->rows!==null && count($model->rows)>0){
			$repRows=$model->rows;
			foreach($repRows as $m=>$repRow){
				$reportRow=$dom->createElement("rows");
				$reportRow->setAttribute("sortorder",$repRow->sortOrder);
				$reportRow->setAttribute("fieldname",$repRow->fieldName);
				$reportRow->setAttribute("fieldwidth",$repRow->fieldWidth);
				$reportRow->setAttribute("row",$repRow->row);
				$reportRow->setAttribute("issummed",$repRow->isSummed?'true':'false');
				$reportRow->setAttribute("isalignright",$repRow->isAlignRight?'true':'false');
				$reportRow->setAttribute("isdate",$repRow->isDate?'true':'false');
				$reportRow->setAttribute("isdecimal",$repRow->isDecimal?'true':'false');
				$this->appendTextNode($dom,$reportRow,"fieldcalc",$repRow->fieldCalc);
				$report->appendChild($reportRow);
			}
		}
		$root->appendChild($report);
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
	 * Export all (or one) report
	 * 
	 */
	public function actionExportAll()
	{
		$dom=new DomDocument();
		$dom->encoding='utf-8';
		$root = $dom->createElement("lazy8webportreport");
		$root->setAttribute("version","1.00");
		
		$models=Report::model()->findAll();
		foreach($models as $model){
			$this->ExportOneReport($model,$dom,$root);
		}
		
		$dom->appendChild($root);
		$dom->formatOutput = true; 			
		$thefile= $dom->saveXML(); 			
		// set headers
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Content-Description: File Transfer");
		header("Content-Type: text/xml");
		header("Content-Disposition: attachment; filename=\"lazy8webExport.AllReports." . date('Y-m-d_H.i.s') . ".xml\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . strlen($thefile));
		//flush();
					
		print $thefile;
	}
	
	/**
	 * Re install all standard reports
	 * 
	 */
	public function actionReload($isShowAdmin=true)
	{
		//delete any existing, known standard reports.
		//install the standard reports
		$this->errors=$this->importFile(dirname(__FILE__).DIRECTORY_SEPARATOR.'lazy8webreport.xml',true,true);
		$this->hasErrors=count($this->errors)>0;
		
		//show the admin screen
		if($isShowAdmin)
			$this->actionAdmin();
	}
	
	/**
	 * Import a report
	 * 
	 */
	public function actionImport()
	{
		if(isset($_POST['importnow']) || isset($_FILES['importfile']))
		{
			$this->hasErrors=false;
			$this->errors=array(array());
			if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'../..'.'/assets/uploadrep.sql'))
				unlink(dirname(__FILE__).DIRECTORY_SEPARATOR.'../..'.'/assets/uploadrep.sql');
			if($_FILES['importfile']['error']<>0){
				$this->hasErrors=true;
				if($_FILES['importfile']['error']==4)
					$this->errors=array(array(Yii::t('lazy8','Returned error = 4 which means no file given'),Yii::t('lazy8','Select a file and try again')));
				else
					$this->errors=array(array(Yii::t('lazy8','Returned error') . ' = '. $_FILES['importfile']['error'],Yii::t('lazy8','Select a file and try again')));
			}else{
				$importFile=CUploadedFile::getInstanceByName('importfile');
				$importFile->saveAs(dirname(__FILE__).DIRECTORY_SEPARATOR.'../..'.'/assets/uploadrep.sql');
				$this->errors=$this->importFile(dirname(__FILE__).DIRECTORY_SEPARATOR.'../..'.'/assets/uploadrep.sql');
				$this->hasErrors=count($this->errors)>0;
			}
		}else if(isset($_GET['importing'])){
			$this->hasErrors=true;
			$this->errors=array(array(Yii::t('lazy8','Upload failed.  Possibly the file was too big.'),Yii::t('lazy8','Select a file and try again')));
		}else{
			$this->hasErrors=false;
			$this->errors=array(array());
		}
		$this->render('importrep');
	}
	
	/**
	 * Import a report
	 * 
	 */
	public static function importFile($filename,$showMessage=true,$removeExistingReport=false)
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
		if($root->nodeName!="lazy8webportreport"){
			if($showMessage)
				$localErrors=array(array(Yii::t('lazy8','Upload failed.  This is not a valid file.'),Yii::t('lazy8','Select a file and try again')));
			return $localErrors;
		}
		if($root->getAttribute('version')>1.00){
			if($showMessage)
				$localErrors=array(array(Yii::t('lazy8','There maybe problems because this is a file version greater then this programs version'),Yii::t('lazy8','Select a file and try again')));
		}
		
		$nodeReports = $root->getElementsByTagName('report');
		unset($root);
		unset($dom);
		foreach($nodeReports as $nodeReport){
			if($removeExistingReport){
				$deleteReports=Report::model()->findAll(array('condition'=>'name=\''. $nodeReport->getAttribute('name') . '\''));
				if($deleteReports!=null){
					foreach($deleteReports as $deleteReport)
						$deleteReport->delete();
				}
			}
			$report=new Report();
			$report->name=$nodeReport->getAttribute('name');
			$report->desc=ReportController::getNodeText($nodeReport,"desc");
			$report->selectSql=ReportController::getNodeText($nodeReport,"selectsql");
			$report->sortOrder=ReportController::getNodeText($nodeReport,"sortOrder");
			$report->cssColorFileName=ReportController::getNodeText($nodeReport,"csscolorfilename");
			$report->cssBwFileName=ReportController::getNodeText($nodeReport,"cssbwfilename");
			$nodeParams = $nodeReport->getElementsByTagName('parameter');
			if(!$report->save()){
				if($showMessage)
					$localErrors=$report->getErrors();
				return $localErrors;
			}
			foreach($nodeParams as $nodeParam){
				$reportParam=new ReportParameters();
				$reportParam->reportId=$report->id;
				$reportParam->sortOrder=$nodeParam->getAttribute('sortorder');
				$reportParam->name=$nodeParam->getAttribute('name');
				$reportParam->alias=$nodeParam->getAttribute('alias');
				$reportParam->dataType=$nodeParam->getAttribute('datatype');
				$reportParam->isDefaultPhp=$nodeParam->getAttribute('isdefaultphp')=='true'?1:0;
				$reportParam->isDate=$nodeParam->getAttribute('isdate')=='true'?1:0;
				$reportParam->isDecimal=$nodeParam->getAttribute('isdecimal')=='true'?1:0;
				$reportParam->desc=ReportController::getNodeText($nodeParam,"desc");
				$reportParam->phpSecondaryInfo=ReportController::getNodeText($nodeParam,"phpsecondaryinfo");
				$reportParam->defaultValue=ReportController::getNodeText($nodeParam,"defaultvalue");	
				if(!$reportParam->save()){
					if($showMessage)
						$localErrors=$reportParam->getErrors();
				}
			}
			unset($nodeParams);
			$nodeGroups = $nodeReport->getElementsByTagName('group');
			foreach($nodeGroups as $nodeGroup){
				$reportGroup=new ReportGroups();
				$reportGroup->reportId=$report->id;
				$reportGroup->sortOrder=$nodeGroup->getAttribute('sortorder');
				$reportGroup->breakingField=$nodeGroup->getAttribute('breakingfield');
				$reportGroup->pageBreak=$nodeGroup->getAttribute('pagebreak')=='true'?1:0;
				$reportGroup->showGrid=$nodeGroup->getAttribute('showgrid')=='true'?1:0;
				$reportGroup->showHeader=$nodeGroup->getAttribute('showheader')=='true'?1:0;
				$reportGroup->continueSumsOverGroup=$nodeGroup->getAttribute('continuesumsovergroup')=='true'?1:0;
				if(!$reportGroup->save()){
					if($showMessage)
						$localErrors=$reportGroup->getErrors();
				}
				$nodeGroupFields = $nodeGroup->getElementsByTagName('field');
				foreach($nodeGroupFields as $nodeGroupField){
					$reportGroupField=new ReportGroupFields();
					$reportGroupField->reportGroupId=$reportGroup->id;
					$reportGroupField->sortOrder=$nodeGroupField->getAttribute('sortorder');
					$reportGroupField->fieldName=$nodeGroupField->getAttribute('fieldname');
					$reportGroupField->fieldWidth=$nodeGroupField->getAttribute('fieldwidth');
					$reportGroupField->row=$nodeGroupField->getAttribute('row');
					$reportGroupField->isDate=$nodeGroupField->getAttribute('isdate')=='true'?1:0;
					$reportGroupField->isDecimal=$nodeGroupField->getAttribute('isdecimal')=='true'?1:0;
					$reportGroupField->fieldCalc=ReportController::getNodeText($nodeGroupField,"fieldcalc");								
					if(!$reportGroupField->save()){
						if($showMessage)
							$localErrors=$reportGroupField->getErrors();
					}
				}
				unset($nodeGroupFields);
			}
			unset($nodeGroups);
			$nodeReportRows = $nodeReport->getElementsByTagName('rows');
			foreach($nodeReportRows as $nodeReportRow){
				$reportRow=new ReportRows();
				$reportRow->reportId=$report->id;
				$reportRow->sortOrder=$nodeReportRow->getAttribute('sortorder');
				$reportRow->fieldName=$nodeReportRow->getAttribute('fieldname');
				$reportRow->fieldWidth=$nodeReportRow->getAttribute('fieldwidth');
				$reportRow->row=$nodeReportRow->getAttribute('row');
				$reportRow->isSummed=$nodeReportRow->getAttribute('issummed')=='true'?1:0;
				$reportRow->isAlignRight=$nodeReportRow->getAttribute('isalignright')=='true'?1:0;
				$reportRow->isDate=$nodeReportRow->getAttribute('isdate')=='true'?1:0;
				$reportRow->isDecimal=$nodeReportRow->getAttribute('isdecimal')=='true'?1:0;
				$reportRow->fieldCalc=ReportController::getNodeText($nodeReportRow,"fieldcalc");								
				if(!$reportRow->save()){
					if($showMessage)
						$localErrors=$reportRow->getErrors();
				}
			}
		}
		return $localErrors;
	}
}
