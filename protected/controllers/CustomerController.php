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


class CustomerController extends CController
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
				'actions'=>array('create','update','admin'),
				'expression'=>'Yii::app()->user->getState(\'allowCustomer\')',
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
		$model=new Customer;
		if(isset($_POST['Customer']))
		{
			$model->attributes=$_POST['Customer'];
			$model->companyId=Yii::app()->user->getState('selectedCompanyId');
			if($model->save()){
				ChangeLog::addLog('ADD','Customer',$model->toString());
				$this->redirect(array('admin','id'=>$model->id));
			}
		}
		$model->dateChanged=User::getDateFormatted(date('Y-m-d'));
		$this->render('create',array('model'=>$model));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionUpdate()
	{
		$model=$this->loadCustomer();
		if(isset($_POST['Customer']))
		{
			$modelBeforeChange=$model->toString();
			$model->attributes=$_POST['Customer'];
			$model->companyId=Yii::app()->user->getState('selectedCompanyId');
			if($model->save()){
				$stringModel=$model->toString();
				if ($modelBeforeChange!=$stringModel)
					ChangeLog::addLog('UPDATE','Customer','BEFORE<br />' . $modelBeforeChange . '<br />AFTER<br />' . $stringModel);
				$this->redirect(array('admin','id'=>$model->id));
			}
		}
		$this->render('update',array('model'=>$model));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$this->processAdminCommand();

		$criteria=new CDbCriteria;
		$criteria->addSearchCondition('companyId',Yii::app()->user->getState('selectedCompanyId'));

		$pages=new CPagination(Customer::model()->count($criteria));
		$pages->pageSize=Yii::app()->user->getState('NumberRecordsPerPage');
		$pages->applyLimit($criteria);

		$sort=new CSort('Customer');
		$sort->applyOrder($criteria);

		$models=Customer::model()->findAll($criteria);

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
	public function loadCustomer($id=null)
	{
		if($this->_model===null)
		{
			if($id!==null || isset($_GET['id']))
				$this->_model=Customer::model()->findbyPk($id!==null ? $id : $_GET['id']);
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
			$deleteCustomer=$this->loadCustomer($_POST['id']);
			ChangeLog::addLog('DELETE','Customer',$deleteCustomer->toString());
			$deleteCustomer->delete();
			// reload the current page to avoid duplicated delete actions
			$this->refresh();
		}
	}
}
