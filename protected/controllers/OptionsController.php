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


class OptionsController extends CController
{
	

	/**
	 * @var string specifies the default action to be 'list'.
	 */
	public $defaultAction='weboptions';

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
				'actions'=>array('weboptions'),
				'expression'=>'Yii::app()->user->getState(\'allowAdmin\')',
			),
			array('allow', 
				'actions'=>array('companyuseroptions'),
				'expression'=>'Yii::app()->user->getState(\'allowAdmin\')',
			),
			
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	/**
	 * Updates options for a user with respect to a company
	 * 
	 */
	public function actionCompanyuseroptions()
	{
		if(isset($_POST['options_posted']))
		{
			User::updateOptionTemplate(User::optionsCompanyUserTemplate(),$_POST['userId'],$_POST['companyId']);
			$this->redirect(array('user/addcompanies','id'=>$_POST['userId']));
		}else{
			$usersModel=User::model()->findbyPk($_GET['userId']);
			$compModel=Company::model()->findbyPk($_GET['companyId']);
			$this->render('companyuseroptions',array(
				'companyId'=>$_GET['companyId'],
				'id'=>$_GET['userId'],
				'usersDisplayname'=>$usersModel->displayname,
				'companyName'=>$compModel->name,
				'weboptions'=>Options::model()->findAll('companyId=:comp AND userId=:id', array(':comp'=>$_GET['companyId'],':id'=>$_GET['userId']))
				));
		}
	}


	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionWeboptions()
	{
		if(isset($_POST['options_posted']))
		{
			User::updateOptionTemplate(User::optionsWebTemplate(),0,0);
			$this->redirect(Yii::app()->request->baseUrl);
		}else{
			$this->render('weboptions',array(
				'weboptions'=>Options::model()->findAll('companyId=0 AND userId=0', array())
				));
		}
	}

}
