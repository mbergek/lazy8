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


class CompanyController extends CController
{
	

	/**
	 * @var string specifies the default action to be 'list'.
	 */
	public $defaultAction='admin';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $lastImportedPeriod;
	private $_model;
	public $hasErrors;
	public $errors;
	public function getErrors(){return $this->errors;}

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
				'actions'=>array('create'),
				'expression'=>'Yii::app()->user->getState(\'allowCompanyCreation\') || Yii::app()->user->getState(\'allowAdmin\') ',
			),
			array('allow', 
				'actions'=>array('update','admin'),
				'expression'=>'Yii::app()->user->getState(\'allowCompany\')  || Yii::app()->user->getState(\'allowAdmin\') ',
			),
			array('allow', 
				'actions'=>array('export'),
				'expression'=>'Yii::app()->user->getState(\'allowCompanyExport\')',
			),
			array('allow', 
				'actions'=>array('exportall'),
				'expression'=>'Yii::app()->user->getState(\'allowExportAll\')',
			),
			array('allow', 
				'actions'=>array('import','importEnglish','importSwedish'),
				'expression'=>'Yii::app()->user->getState(\'allowImport\')',
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
		$model=new Company;
		if(isset($_POST['Company']))
		{
			$model->attributes=$_POST['Company'];
			if($model->save()){
				User::updateOptionTemplate(User::optionsCompanyTemplate(),0,$model->id);
				ChangeLog::addLog('ADD','Company',$model->toString());
				$this->updateUserEditingRightsForCompany($model->id,0);
				$this->redirect(array('period/create'));
			}
		}
		$compOptions=User::optionsCompanyTemplate();
		$options=array();
		foreach($compOptions as $key=>$option){
			$newOp=new Options();
			$newOp->name=$key;
			$newOp->datavalue=$option[1];
			$options[]=$newOp;
		}
		$model->dateChanged=User::getDateFormatted(date('Y-m-d'));
		$this->render('create',array('model'=>$model,'options'=>$options));
	}


	/**
	 * Search account model for the given code and return the AccountId.
	 */
	private function FindAccountIdFromCode($accountCode,$companyId,$allAccs,$errorIdText,$errors=null,$isReportNotNullNotFound=false,$isReportNull=false){
		if( $accountCode==0 || $accountCode==null || $accountCode==""){
			if( $isReportNull ){
				$errors.add(Yii::t('lazy8',"Account code is null"). ' = ' .$errorIdText);
			}
			return 0;
		}else{
			$id=null;
			$id=$allAccs[$accountCode];
			if($id===null && $isReportNotNullNotFound){
				$errors[]=Yii::t('lazy8',"Account code not found") . " " . $accountCode . ' ; ' . $errorIdText;
				return 0;
			}
			return $id;
		}
		
	}

	/**
	 * Search customer model for the given code and return the customerId.
	 */
	private function FindCustomerIdFromCode($customerCode,$companyId,$allCusts,$errorIdText,$errors=null,$isReportNotNullNotFound=false,$isReportNull=false){
		if( $customerCode==0 || $customerCode==null || $customerCode==""){
			if( $isReportNull ){
				$errors.add(Yii::t('lazy8',"Customer code is null"). ' = ' .$errorIdText);
			}
			return 0;
		}else{
			$id=null;
			$id=$allCusts[$customerCode];
			if($id===null && $isReportNotNullNotFound){
				$errors[]=Yii::t('lazy8',"Customer code not found") . " " . $customerCode . ' ; ' . $errorIdText;
				return 0;
			}
			return $id;
		}
		
	}
	private function updateUserEditingRightsForCompany($compId,$periodId)
	{
		//we need to give the user editing rights for this company
		$usersModel=User::model()->findbyPk(Yii::app()->user->id);
		$usersModel->selectedCompanyId= $compId;
		$usersModel->selectedPeriodId=$periodId;
		$usersModel->confirmPassword=$usersModel->password;
		$usersModel->save();
		User::model()->dbConnection->createCommand("INSERT INTO CompanyUser (userId, companyId) VALUES (".Yii::app()->user->id.",{$compId})")->execute();
		User::setOptionStatesAndControlTable(false,false,Yii::app()->user,User::optionsCompanyTemplate(),$compId,0);
		$usersModel->setStates(true);
	}
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionImportSwedish()
	{
		$id=$this->importCompany(dirname(__FILE__).DIRECTORY_SEPARATOR."DefaultCompany.SE.xml");
		if($id>0){
			$this->updateUserEditingRightsForCompany($id,$this->lastImportedPeriod);
		}
		$this->redirect(array('admin'));
	}
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionImportEnglish()
	{
		$id=$this->importCompany(dirname(__FILE__).DIRECTORY_SEPARATOR."DefaultCompany.US.xml");
		if($id>0){
			$this->updateUserEditingRightsForCompany($id,$this->lastImportedPeriod);
		}
		$this->redirect(array('admin'));
	}
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionImport()
	{
		if(isset($_POST['importnow']) || isset($_FILES['importfile']))
		{
			$this->hasErrors=false;
			$this->errors=array(array());
			if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'../..'.'/assets/upload.sql'))
				unlink(dirname(__FILE__).DIRECTORY_SEPARATOR.'../..'.'/assets/upload.sql');
			if($_FILES['importfile']['error']<>0){
				$this->hasErrors=true;
				if($_FILES['importfile']['error']==4)
					$this->errors=array(array(Yii::t('lazy8','Returned error = 4 which means no file given'),Yii::t('lazy8','Select a file and try again')));
				else
					$this->errors=array(array(Yii::t('lazy8','Returned error') . ' = '. $_FILES['importfile']['error'],Yii::t('lazy8','Select a file and try again')));
			}else{
				$importFile=CUploadedFile::getInstanceByName('importfile');
				$importFile->saveAs(dirname(__FILE__).DIRECTORY_SEPARATOR.'../..'.'/assets/upload.sql');
				$id=$this->importCompany(dirname(__FILE__).DIRECTORY_SEPARATOR.'../..'.'/assets/upload.sql');
				if($id>0){
					$this->updateUserEditingRightsForCompany($id,$this->lastImportedPeriod);
				}
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
	private function importCompany($fileNameAndPath)
	{
		$allAccounts=array();
		$dom = new domDocument();
		if( ! $dom->load($fileNameAndPath) ){
			throw new CException(Yii::t('lazy8','input file could not be xml parsed'));
		}
		
		$root = $dom->documentElement;
		if($root->nodeName!="lazy8webport"){
			$this->hasErrors=true;
			$this->errors=array(array(Yii::t('lazy8','Upload failed.  This is not a valid file.'),Yii::t('lazy8','Select a file and try again')));
			$this->render('showimport');
			return 0;
		}
		if($root->getAttribute('version')>1.00)
			$this->errors=array(array(Yii::t('lazy8','There maybe problems because this is a file version greater then this programs version'),Yii::t('lazy8','Select a file and try again')));
		$nodeCompanys = $root->getElementsByTagName('company');
		unset($root);
		unset($dom);
		$this->lastImportedPeriod=0;
		foreach($nodeCompanys as $nodeCompany){
			//make sure the company code is unique
			$modelCompany=new Company;
			$code=$nodeCompany->getAttribute('code');
			$code--;
			//make sure the company code is valid. Change if not.
			do{
				$code++;
				$comptest=Company::model()->find(array('condition'=>'code='.$code));
			}while($comptest!==null);
			//create the company
			$modelCompany=new Company;
			$modelCompany->code=$code;
			$modelCompany->name=$nodeCompany->getAttribute('name');
			$modelCompany->lastAbsTransNum=$nodeCompany->getAttribute('lastAbsTransNum');
			if(!$modelCompany->save())
				throw new CException(Yii::t('lazy8','Could not create the company, bad paramters').';'.var_export($modelCompany->getErrors()));
			try{				
				$allAccounts=array();
				$nodesAccountTypes = $nodeCompany->getElementsByTagName('accounttype');
				foreach($nodesAccountTypes as $nodeAccountType){
					$modelAccountType=new AccountType;
					$modelAccountType->companyId=$modelCompany->id;
					$modelAccountType->code=$nodeAccountType->getAttribute('code');
					$modelAccountType->name=$nodeAccountType->getAttribute('name');
					$modelAccountType->sortOrder=$nodeAccountType->getAttribute('sortorder');
					$modelAccountType->isInBalance=$nodeAccountType->getAttribute('isinbalance')=="1"?1:0;
					if(!$modelAccountType->save()){
						$modelCompany->delete();
						throw new CException(Yii::t('lazy8','Could not create the AccountType, bad paramters').';name='.$modelAccountType->name.';'.serialize($modelAccountType->getErrors()));
					}
					$nodesAccounts = $nodeAccountType->getElementsByTagName('account');
					foreach($nodesAccounts as $nodeAccount){
						$modelAccount=new Account;
						$modelAccount->companyId=$modelCompany->id;
						$modelAccount->code=$nodeAccount->getAttribute('code');
						$modelAccount->accountTypeId=$modelAccountType->id;
						$modelAccount->name=$nodeAccount->getAttribute('name');
						if(!$modelAccount->save()){
							$modelCompany->delete();
							throw new CException(Yii::t('lazy8','Could not create the Account, bad paramters').';'.serialize($modelAccount->getErrors()));
						}
						$allAccounts[$modelAccount->code]=$modelAccount->id;
						unset($nodeAccount);
						unset($modelAccount);
					}
					unset($modelAccountType);
					unset($nodeAccountType);
				}
				unset($nodesAccountTypes);
				$allCustomers=array();
				$nodesCustomers = $nodeCompany->getElementsByTagName('customer');
				foreach($nodesCustomers as $nodeCustomer){
					$modelCustomer=new Customer;
					$modelCustomer->companyId=$modelCompany->id;
					$modelCustomer->code=$nodeCustomer->getAttribute('code');
					$modelCustomer->accountId=$this->FindAccountIdFromCode($nodeCustomer->getAttribute('accountcode'),$modelCompany->id,$allAccounts,'customercode='.$modelCustomer->code,$this->errors,true);
					$modelCustomer->name=$nodeCustomer->getAttribute('name');
					$modelCustomer->desc=$nodeCustomer->getAttribute('desc');
					if(!$modelCustomer->save()){
						$modelCompany->delete();
						throw new CException(Yii::t('lazy8','Could not create the Customer, bad paramters').';'.serialize($modelCustomer->getErrors()));
					}
					$allCustomers[$modelCustomer->code]=$modelCustomer->id;
					unset($modelCustomer);
					unset($nodeCustomer);
				}
				unset($nodesCustomers);
				$nodesPeriods = $nodeCompany->getElementsByTagName('period');
				foreach($nodesPeriods as $nodePeriod){
					$modelPeriod=new Period;
					$modelPeriod->companyId=$modelCompany->id;
					$modelPeriod->dateStart=$nodePeriod->getAttribute('datestart');
					$modelPeriod->dateEnd=$nodePeriod->getAttribute('dateend');
					$modelPeriod->lastPeriodTransNum=$nodePeriod->getAttribute('lastperiodtransnum');
					if(!$modelPeriod->save()){
						$modelCompany->delete();
						throw new CException(Yii::t('lazy8','Could not create the period, bad paramters').';'.serialize($modelPeriod->getErrors()));
					}
					$this->lastImportedPeriod=$modelPeriod->id;
					$nodesTransactions = $nodePeriod->getElementsByTagName('transaction');
					foreach($nodesTransactions as $nodeTransaction){
						$modelTransaction=new Trans;
						$modelTransaction->companyId=$modelCompany->id;
						$modelTransaction->companyNum=$nodeTransaction->getAttribute('code');
						$modelTransaction->periodId=$modelPeriod->id;
						$modelTransaction->periodNum=$nodeTransaction->getAttribute('periodnum');
						$modelTransaction->regDate=$nodeTransaction->getAttribute('regdate');
						$modelTransaction->invDate=$nodeTransaction->getAttribute('invdate');
						$modelTransaction->notes=$nodeTransaction->getAttribute('notes');
						$modelTransaction->fileInfo=$nodeTransaction->getAttribute('fileinfo');
						if(!$modelTransaction->save()){
							$modelCompany->delete();						
							throw new CException(Yii::t('lazy8','Could not create the Transaction, bad paramters').';'.serialize($modelTransaction->getErrors()));
						}
						$nodesTransactionAmounts = $nodeTransaction->getElementsByTagName('amount');
						foreach($nodesTransactionAmounts as $nodeTransactionAmount){
							$modelTransRow=new TransRow;
							$modelTransRow->transId=$modelTransaction->id;
							$modelTransRow->accountId=$this->FindAccountIdFromCode($nodeTransactionAmount->getAttribute('accountcode'),$modelCompany->id,$allAccounts,'TransCode='.$modelTransaction->companyNum,$this->errors,true,true);
							$modelTransRow->customerId=$this->FindCustomerIdFromCode($nodeTransactionAmount->getAttribute('customercode'),$modelCompany->id,$allCustomers,'TransCode='.$modelTransaction->companyNum,$this->errors,true);
							$modelTransRow->notes=$nodeTransactionAmount->getAttribute('notes');
							$modelTransRow->amount=$nodeTransactionAmount->getAttribute('amount');
							if(!$modelTransRow->save()){
								$modelCompany->delete();
								throw new CException(Yii::t('lazy8','Could not create the TransactionAmount, bad paramters').';'.serialize($modelTransRow->getErrors()));
							}
							unset($modelTransRow);
						}
						unset($modelTransaction);
						unset($nodesTransactionAmounts);
						unset($nodeTransaction);
					}
					unset($modelPeriod);
					unset($nodePeriod);
					unset($nodesTransactions);
				}
				unset($nodesPeriods);
			}catch(Exception $e){
				$modelCompany->delete();
				throw $e;
			}
			$errors=array();//we ignore the errors...
			yii::app()->onImport(new Lazy8Event(array('importobject'=>'Company','root'=>$nodeCompany,'errors'=>$errors),$modelCompany->id));
			unset($nodeCompany);
		}
		unset($nodeCompanys);
		ChangeLog::addLog('ADD','Company','Imported company '. $modelCompany->toString());
		return $modelCompany->id;
	}
	public static function replace_bad_filename_chars($filename) {
	  $filtered_filename = "";
	
	  $patterns = array(
	    "/\s/", # Whitespace
	    "/\&/", # Ampersand
	    "/\+/"  # Plus
	  );
	  $replacements = array(
	    "_",   # Whitespace
	    "and", # Ampersand
	    "plus" # Plus
	  );
	  
	  $filename = preg_replace($patterns,$replacements,$filename);
	  for ($i=0;$i<strlen($filename);$i++) {
	    $current_char = substr($filename,$i,1);
	    if (ctype_alnum($current_char) == TRUE || $current_char == "_" || $current_char == ".") {
	      $filtered_filename .= $current_char;
	    }
	  }     
		
	  return $filtered_filename;
	}

	private function exportCompany($companyId=null){
		$compName="AllCompanies";
		if($companyId!=null)
			$compName=CompanyController::replace_bad_filename_chars(Company::model()->findbyPk($companyId)->name);
		// set headers
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Content-Description: File Transfer");
		header("Content-Type: text/xml");
		header("Content-Disposition: attachment; filename=\"lazy8webExport.Company.". $compName . "." . date('Y-m-d_H.i.s') . ".xml\"");
		header("Content-Transfer-Encoding: binary");
		//safari can't deal with this header length zero
		//		header("Content-Length: " );

		$writer = new XMLWriter();
		// Output directly to the user
		//dirname(__FILE__).DIRECTORY_SEPARATOR.'../..'.'/assets/upload.sql'
		$writer->openURI('php://output');
		$writer->startDocument('1.0','utf-8');
		
		$writer->setIndent(4);
		$writer->startElement('lazy8webport');
		$writer->writeAttribute('version', '1.00');
		$companies=array();
		if(isset($companyId)){
			$companies[]=Company::model()->findbyPk($companyId);
		}else{
			$companies= Company::model()->findAll(array('order'=>'code'));
		}
		foreach($companies as $company){
			ChangeLog::addLog('OTHER','Company','Exported company '. $company->toString());
			$writer->startElement('company');
			$writer->writeAttribute("code",$company->code);
			$writer->writeAttribute("name",$company->name);
			$writer->writeAttribute("lastAbsTransNum",$company->lastAbsTransNum);
			$allAccounts=array();
			$accountTypes= AccountType::model()->findAll(array('condition'=>'companyId='.$company->id,'order'=>'code'));
			foreach($accountTypes as $accountType){
				$writer->startElement('accounttype');
				$writer->writeAttribute("code",$accountType->code);
				$writer->writeAttribute("name",$accountType->name);
				$writer->writeAttribute("sortorder",$accountType->sortOrder);
				$writer->writeAttribute("isinbalance",$accountType->isInBalance);
				$accounts= Account::model()->findAll(array('condition'=>'companyId='.$company->id . ' AND accountTypeId='. $accountType->id,'order'=>'code'));
				foreach($accounts as $account){
					$allAccounts[$account->id]=$account->code;
					$writer->startElement('account');
					$writer->writeAttribute("code",$account->code);
					$writer->writeAttribute("name",$account->name);
					$writer->endElement();
				}
				$writer->endElement();
			}
			$customers= Customer::model()->findAll(array('condition'=>'companyId='.$company->id,'order'=>'code'));
			$allCustomers=array();
			foreach($customers as $customer){
				$writer->startElement('customer');
				$allCustomers[$customer->id]=$customer->code;
				$writer->writeAttribute("code",$customer->code);
				$writer->writeAttribute("name",$customer->name);
				$writer->writeAttribute("desc",$customer->desc);
				if(isset($allAccounts[$customer->accountId]))
					$writer->writeAttribute("accountcode",$allAccounts[$customer->accountId]);
				else
					$writer->writeAttribute("accountcode",0);
				$writer->endElement();
			}
			$periods= Period::model()->findAll(array('condition'=>'companyId='.$company->id,'order'=>'dateStart'));
			foreach($periods as $period){
				$writer->startElement('period');
				$writer->writeAttribute("datestart",$period->dateStart);
				$writer->writeAttribute("dateend",$period->dateEnd);
				$writer->writeAttribute("lastperiodtransnum",$period->lastPeriodTransNum);
				$transactions= Trans::model()->findAll(array('condition'=>'companyId='.$company->id . ' AND periodId='. $period->id,'order'=>'companyNum'));
				foreach($transactions as $transaction){
					$writer->startElement('transaction');
					$writer->writeAttribute("code",$transaction->companyNum);
					$writer->writeAttribute("periodnum",$transaction->periodNum);
					$writer->writeAttribute("regdate",$transaction->regDate);
					$writer->writeAttribute("invdate",$transaction->invDate);
					$writer->writeAttribute("notes",$transaction->notes);
					$writer->writeAttribute("fileinfo",$transaction->fileInfo);
					$transrows= TransRow::model()->findAll(array('condition'=>'transId='.$transaction->id,'order'=>'amount DESC'));
					foreach($transrows as $transrow){
						$writer->startElement('amount');
						$writer->writeAttribute("accountcode",isset($allAccounts[$transrow->accountId])?$allAccounts[$transrow->accountId]:0);
						$writer->writeAttribute("customercode",isset($allCustomers[$transrow->customerId])?$allCustomers[$transrow->customerId]:0);
						$writer->writeAttribute("notes",$transrow->notes);
						$writer->writeAttribute("amount",$transrow->amount);
						$writer->endElement();
					}
					$writer->endElement();
				}
				$writer->endElement();
			}
			yii::app()->onExport(new Lazy8Event(array('exportobject'=>'Company','writer'=>$writer),$company->id));
			
			$writer->endElement();
		}
		$writer->endElement();
		$writer->endDocument();
		
		$writer->flush();
		return;//we may not send any more to the screen or it will mess up the file we just sent!
	}
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionExport()
	{
		//echo $_POST['id'];
		//die();
		$model=$this->loadCompany();
		//die();
		$this->exportCompany($model->id);
		//send nothing more otherwise the file will be ruined.
		//$this->render('create',array('model'=>$model));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionExportAll()
	{
		$this->exportCompany();
		//send nothing more otherwise the file will be ruined.
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionUpdate()
	{
		$model=$this->loadCompany();
		if(isset($_POST['Company']))
		{
			$modelBeforeChange=$model->toString();
			$model->attributes=$_POST['Company'];
			if($model->save()){
				User::updateOptionTemplate(User::optionsCompanyTemplate(),0,$model->id);
				$stringModel=$model->toString();
				if ($modelBeforeChange!=$stringModel)
					ChangeLog::addLog('UPDATE','Company','BEFORE<br />' . $modelBeforeChange . '<br />AFTER<br />' . $stringModel);
				$this->redirect(array('admin','id'=>$model->id));
			}
		}
		User::setOptionStatesAndControlTable(false,false,Yii::app()->user,User::optionsCompanyTemplate(),$model->id,0);
		$criteria=new CDbCriteria;
		$criteria->compare('companyId',$model->id);
		$criteria->compare('userId',0);
		$options=Options::model()->findAll($criteria);		
		$this->render('update',array('model'=>$model,'options'=>$options));
	}


	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$this->processAdminCommand();

		$criteria=new CDbCriteria;

		$pages=new CPagination(Company::model()->count($criteria));
		$pages->pageSize=Yii::app()->user->getState('NumberRecordsPerPage');
		$pages->applyLimit($criteria);

		$sort=new CSort('Company');
		$sort->applyOrder($criteria);

		$models=Company::model()->findAll($criteria);

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
	public function loadCompany($id=null)
	{
		if($this->_model===null)
		{
			if($id!==null || isset($_GET['id']))
				$this->_model=Company::model()->findbyPk($id!==null ? $id : $_GET['id']);
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
			$deletecompany=$this->loadCompany($_POST['id']);
			ChangeLog::addLog('DELETE','Company',$deletecompany->toString());
			$deletecompany->delete();
			$usersModel=User::model()->findbyPk(Yii::app()->user->id);
			$usersModel->setStates(true);
			// reload the current page to avoid duplicated delete actions
			$this->refresh();
		}
	}
}
