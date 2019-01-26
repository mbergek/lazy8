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


class UserController extends CController
{
	

	/**
	 * @var string specifies the default action to be 'list'.
	 */
	public $defaultAction='admin';

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
				'actions'=>array('selectcompany'),
				'expression'=>'Yii::app()->user->getState(\'allowPeriodSelection\')',
			),
			array('allow', 
				'actions'=>array('update'),
				'expression'=>'Yii::app()->user->getState(\'allowAdmin\') || Yii::app()->user->getState(\'allowSelf\') || Yii::app()->user->getState(\'allowAdmin\')',
			),
			array('allow', 
				'actions'=>array('addcompanies'),
				'expression'=>'Yii::app()->user->getState(\'allowCompanyCreation\')',
			),
			array('allow', 
				'actions'=>array('admin','delete','create'),
				'expression'=>'Yii::app()->user->getState(\'allowAdmin\')',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	private function setOptionsByTemplate($forceDefaultAdminOnCompany)
	{
		$options=User::optionsCompanyUserTemplate();
		$optionsUser=User::optionsUserTemplate();
		if(isset($_POST['Editor'])){
			foreach($options as $key=>$option)
				$_POST['option_' . $key]=$option[5]=='true'?1:0;
			//preset with the default value for the user options
			foreach($optionsUser as $key=>$option)
				$_POST['option_' . $key]=$option[1];
			foreach($optionsUser as $key=>$option)
				if($option[7]=='true')
					$_POST['option_' . $key]=1;
		}elseif(isset($_POST['Viewer'])){
			foreach($options as $key=>$option)
				$_POST['option_' . $key]=$option[6]=='true'?1:0;
			//preset with the default value for the user options
			foreach($optionsUser as $key=>$option)
				$_POST['option_' . $key]=$option[1];
			foreach($optionsUser as $key=>$option)
				if($option[8]=='true')
					$_POST['option_' . $key]=1;
		}elseif(isset($_POST['Admin'])){
			foreach($options as $key=>$option)
				$_POST['option_' . $key]=$option[4]=='true'?1:0;
			//preset with the default value for the user options
			foreach($optionsUser as $key=>$option)
				$_POST['option_' . $key]=$option[1];
			foreach($optionsUser as $key=>$option)
				if($option[6]=='true')
					$_POST['option_' . $key]=1;
		}elseif($forceDefaultAdminOnCompany){
			//default is administrator when creating.
			foreach($options as $key=>$option)
				$_POST['option_' . $key]=$option[4]=='true'?1:0;
		}
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionCreate()
	{
		$model=new User;
		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$model->salt=hash('sha1', uniqid(rand(), true));
			$model->password=hash('sha1',$model->password . $model->salt);
			$model->confirmPassword=hash('sha1',$model->confirmPassword . $model->salt);
			if($model->save()){
				$this->setOptionsByTemplate(true);
				$companies=array();
				if(isset($_POST['companies']))$companies=$_POST['companies'];
				User::updateOptionTemplate(User::optionsUserTemplate(),$model->id,0);
				foreach($companies as $comp){
					User::model()->dbConnection->createCommand("INSERT INTO CompanyUser (userId, companyId) VALUES ({$model->id},{$comp})")->execute();
					User::updateOptionTemplate(User::optionsCompanyUserTemplate(),$model->id,$comp);
				}
				$model->setStates();
				ChangeLog::addLog('ADD','User',$model->toString());
				$this->redirect(array('admin','id'=>$model->id));
			}else{
				unset($this->_model->password);
				unset($this->_model->confirmPassword);
			}
		}
		$usersModel=User::model()->findbyPk($model->id);
		//put  all the user companies in an array
		if(isset($usersModel) && $usersModel!=null)$companiesUsers=$usersModel->companies;
		$selectedCompanies=array();

		if(isset($companiesUsers)){
			foreach($companiesUsers as $companyUser){
				$selectedCompanies[]=$companyUser->id;
			}
		}	
		$allCompanies=Company::model()->findAll();
		$compOptions=User::optionsUserTemplate();
		$UsersOptions=array();
		foreach($compOptions as $key=>$option){
			$newOp=new Options();
			$newOp->name=$key;
			$newOp->datavalue=$option[1];
			$UsersOptions[]=$newOp;
		}
		$model->useroptions=$UsersOptions;
		$model->dateChanged=User::getDateFormatted(date('Y-m-d'));
		$this->render('create',array('model'=>$model,
			'allCompanies'=>$allCompanies,
			'selectedCompanies'=>$selectedCompanies,
			));
	}


	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionUpdate()
	{
		$model=$this->loadUser();
		if(isset($_POST['User']))
		{
			$oldPassword=$model->password;
			$modelBeforeChange=$model->toString();
			$model->attributes=$_POST['User'];
			//salt must contain something
			if(!isset($model->salt) || strlen($model->salt)==0){
				$model->salt=hash('sha1', uniqid(rand(), true));
				//since the salt has changed, We must have a new password.  Force it if it is not changed
				if(strlen($model->password)==0 && strlen($model->confirmPassword==0)){
					//this forces the entry of a new password
					unset($oldPassword);
				}
			}
			//only check the password if they have entered something. Blank password is ignored.
			if(strlen($model->password)==0 && strlen($model->confirmPassword==0)){
				$model->password=$oldPassword;
				$model->confirmPassword=$oldPassword;
			}else{
				$model->password=hash('sha1',$model->password . $model->salt);
				$model->confirmPassword=hash('sha1',$model->confirmPassword . $model->salt);
			}
			if($model->save()){
				if(Yii::app()->user->getState('allowAdmin')){
					//remove all companies for this user
					User::model()->dbConnection->createCommand("DELETE FROM CompanyUser WHERE userId={$model->id}")->execute();
					$companies=array();
					if(isset($_POST['companies']))$companies=$_POST['companies'];
					//must remove all options for unchosen companies.
					$beforeCompanies=$model->companies;
					foreach($beforeCompanies as $beforeComp){
						$foundCompany=false;
						foreach($companies as $comp){
							if($comp==$beforeComp->id){
								$foundCompany=true;
								break;
							}
						}
						if(!$foundCompany){
							//company no longer selected. Delete all options
							User::model()->dbConnection->createCommand("INSERT INTO CompanyUser (userId, companyId) VALUES ({$model->id},{$beforeComp->id})")->execute();
						}
					}
					$foundCurrentCompany=false;
					$foundSelectedCompany=false;
					//re-add the selected companies
					foreach($companies as $comp){
						User::model()->dbConnection->createCommand("INSERT INTO CompanyUser (userId, companyId) VALUES ({$model->id},{$comp})")->execute();
						if($model->selectedCompanyId==$comp)
							$foundCurrentCompany=true;
						if($_POST['companyForOptionsBeforeChange']==$comp)
							$foundSelectedCompany=true;
					}
					if(!$foundCurrentCompany){
						//they have remove the currently selected company.  We must remove it as being selected
						$model->selectedCompanyId=0;
						$model->selectedPeriodId=0;
						$model->confirmPassword=$model->password;
						$model->save();
						if(Yii::app()->user->id==$_GET['id'])
							$model->setStates();
					}
				}
				//do the options
				$this->setOptionsByTemplate(false);
				//user options first
				User::updateOptionTemplate(User::optionsUserTemplate(),$model->id,0,Yii::app()->user->getState('allowAdmin'));
				//company options, only if admin
				if(!isset($_POST['save']) && Yii::app()->user->getState('allowAdmin')){
					//we must set all companies to the template chosen
					foreach($companies as $comp)
						User::updateOptionTemplate(User::optionsCompanyUserTemplate(),$model->id,$comp);
				}elseif(isset($foundSelectedCompany) && $foundSelectedCompany && Yii::app()->user->getState('allowAdmin')){
					//no template chosen.  Just update the visible options for the selected company
					$model->updateOptionTemplate($model->optionsCompanyUserTemplate(),$model->id,$_POST['companyForOptionsBeforeChange']);
				}

				$stringModel=$model->toString();
				if ($modelBeforeChange!=$stringModel)
					ChangeLog::addLog('UPDATE','Account','BEFORE<br />' . $modelBeforeChange . '<br />AFTER<br />' . $stringModel);
				//we need to reload this user to get all the updates in the companies.
				$this->_model=User::model()->findbyPk(isset($id) && $id!==null ? $id : $_GET['id']);
				$model=$this->_model;
//				$this->redirect(array('update','id'=>$model->id));
			}else{
				unset($this->_model->password);
				unset($this->_model->confirmPassword);
			}
		}else{
			unset($this->_model->password);
			unset($this->_model->confirmPassword);
			//make sure the options are all created 
			$this->_model->setStates();
		}
		$companyForOptions=null;
		$companyList=null;
		$options=null;
		$allCompanies=null;
		$selectedCompanies=null;
		if(Yii::app()->user->getState('allowAdmin')==1||Yii::app()->user->getState('allowAdmin')==1)
		{
			$companiesUsers=$model->companies;
			$companyForOptions=null;
			if(isset($companiesUsers) && isset($companiesUsers[0])){
				$companyForOptions=$companiesUsers[0]->id;
			}
			
			if(isset($_POST['companyForOptions']))
				$companyForOptions=$_POST['companyForOptions'];
			$options=Options::model()->findAll('companyId=:comp AND userId=:id', array(':comp'=>$companyForOptions,':id'=>$model->id));
			$companyList=CHtml::listData($companiesUsers,'id','name');
			$usersModel=User::model()->findbyPk($_GET['id']);
			//put  all the user companies in an array
			$companiesUsers=$usersModel->companies;
			$selectedCompanies=array();
	
			if(isset($companiesUsers)){
				foreach($companiesUsers as $companyUser){
					$selectedCompanies[]=$companyUser->id;
				}
			}	
			$allCompanies=Company::model()->findAll();
		}
		$model->password="";
		$model->confirmPassword="";
		$this->render('update',array(
			'model'=>$model,
			'companyForOptions'=>$companyForOptions,
			'companies'=>$companyList,
			'weboptions'=>$options,
			'allCompanies'=>$allCompanies,
			'selectedCompanies'=>$selectedCompanies,
			));
	}

	
	/**
	 * Manages adding companies.
	 */
	public function actionAddcompanies()
	{
		if((isset($_GET['add'])||isset($_GET['remove'])) && isset($_GET['id']))
		{
			if(isset($_GET['add'])){
				User::model()->dbConnection->createCommand("INSERT INTO CompanyUser (userId, companyId) VALUES ({$_GET['id']},{$_GET['companyId']})")->execute();
				$webapp=Yii::app()->user;
				User::setOptionStatesAndControlTable(false,false,Yii::app()->user,User::optionsCompanyUserTemplate(),$_GET['companyId'],$_GET['id']);
			}else{
				//remove it
				User::model()->dbConnection->createCommand("DELETE FROM CompanyUser WHERE userId={$_GET['id']} AND companyId={$_GET['companyId']}")->execute();
				User::model()->dbConnection->createCommand("DELETE FROM Options WHERE userId={$_GET['id']} AND companyId={$_GET['companyId']}")->execute();
				$usersModel=User::model()->findbyPk($_GET['id']);
				if($usersModel->selectedCompanyId==$_GET['companyId']){
					$usersModel->selectedCompanyId=0;
					$usersModel->selectedPeriodId=0;
					$usersModel->confirmPassword=$usersModel->password;
					$usersModel->save();
					if(Yii::app()->user->id==$_GET['id'])
						$usersModel->setStates();
				}
			}
			$this->redirect(array('user/addcompanies','id'=>$_GET['id']));
		}

		$criteria=new CDbCriteria;

		$pages=new CPagination(Company::model()->count($criteria));
		$pages->pageSize=Yii::app()->user->getState('NumberRecordsPerPage');
		$pages->applyLimit($criteria);

		$sort=new CSort('Company');
		$sort->applyOrder($criteria);
		
		$usersModel=User::model()->findbyPk($_GET['id']);
		//put  all the user companies in an array
		$companiesUsers=$usersModel->companies;
		$companyToUserArray=array();

		if(isset($companiesUsers)){
			foreach($companiesUsers as $companyUser){
				$companyToUserArray[$companyUser->id]='';
			}
		}	

		$models=Company::model()->findAll($criteria);
		$this->render('addcompanies',array(
			'models'=>$models,
			'pages'=>$pages,
			'sort'=>$sort,
			'companyToUserArray'=>$companyToUserArray,
			'usersModel'=>$usersModel,
			'id'=>$_GET['id'],
		));
	}

	/**
	 * Manages selecting a company and period.
	 */
	public function actionSelectcompany()
	{
		if(isset($_GET['select']))
		{
			$usersModel=User::model()->findbyPk($_GET['id']);
			$usersModel->selectedCompanyId=$_GET['companyId'];
			$usersModel->selectedPeriodId=$_GET['periodId'];
			$usersModel->confirmPassword=$usersModel->password;
			$usersModel->save();
			//states have now changed.  Need to reset them
			if(Yii::app()->user->id==$_GET['id'])
				$usersModel->setStates();
			$this->redirect(array('user/selectcompany','id'=>$_GET['id']));
		}

		
		$usersModel=User::model()->findbyPk($_GET['id']);
		$this->render('selectcompany',array(
			'usersModel'=>$usersModel,
			'models'=>$usersModel->companies,
			'id'=>$_GET['id'],
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$this->processAdminCommand();

		$criteria=new CDbCriteria;

		$pages=new CPagination(User::model()->count($criteria));
		$pages->pageSize=Yii::app()->user->getState('NumberRecordsPerPage');
		$pages->applyLimit($criteria);

		$sort=new CSort('User');
		$sort->applyOrder($criteria);

		$models=User::model()->findAll($criteria);

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
	public function loadUser($id=null)
	{
		if($this->_model===null)
		{
			if($id!==null || isset($_GET['id']))
				$this->_model=User::model()->findbyPk($id!==null ? $id : $_GET['id']);
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
			$deleteUser=$this->loadUser($_POST['id']);
			ChangeLog::addLog('DELETE','User',$deleteUser->toString());
			$deleteUser->delete();
			// reload the current page to avoid duplicated delete actions
			$this->refresh();
		}
	}
}
