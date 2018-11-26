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


class Company extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Company':
	 * @var integer $id
	 * @var integer $code
	 * @var string $name
	 * @var integer $lastAbsTransNum
	 * @var string $changedBy
	 * @var string $dateChanged
	 */

	 public function toString(){
	 	 return 'id=' . $this->id .';'
	 	 	.'code='.$this->code .  ';' 
	 	 	.'name='.$this->name .  ';' 
	 	 	.'lastAbsTransNum='.$this->lastAbsTransNum .  ';' 
	 	 	. $this->optionsArrayToString();
	 }
	 
	 private function optionsArrayToString(){
		$criteria=new CDbCriteria;
		$criteria->compare('companyId',$this->id);
		$criteria->compare('userId',0);
		$options=Options::model()->findAll($criteria);		
		$returnString='';
		foreach($options as $n=>$option){
			$returnString.=$option->name . '=' . $option->datavalue . ";";
		}
		return $returnString;
	 }

	 /**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'Company';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code, name, lastAbsTransNum', 'required'),
			array('code, lastAbsTransNum', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>100),
			array('dateChanged', 'safe'),
			array('dateChanged','default','value'=>new CDbExpression('NOW()'),'setOnEmpty'=>false,'on'=>'update'),
			array('dateChanged','default','value'=>new CDbExpression('NOW()'),'setOnEmpty'=>false,'on'=>'insert'),
			array('changedBy','default','value'=>Yii::app()->user->name),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'periods'=>array(self::HAS_MANY, 'Period', 'companyId','order'=>'dateStart'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('lazy8','Id'),
			'code' => Yii::t('lazy8','Code'),
			'name' => Yii::t('lazy8','Name'),
			'lastAbsTransNum' => Yii::t('lazy8','Last Abs Trans Num'),
			'userId' => Yii::t('lazy8','User'),
			'dateChanged' => Yii::t('lazy8','Date Changed'),
			'changedBy' => Yii::t('lazy8','Changed by'),
			'actions' => Yii::t('lazy8','Actions'),
		);
	}
	public function delete()
	{
		parent::delete();
		yii::app()->onDeletePost(new Lazy8Event('Company',$this->id));
		$this->dbConnection->createCommand("DELETE FROM Customer WHERE companyId={$this->id}")->execute();
		$this->dbConnection->createCommand("DELETE FROM Period WHERE companyId={$this->id}")->execute();
		$this->dbConnection->createCommand("DELETE FROM Account WHERE companyId={$this->id}")->execute();
		$this->dbConnection->createCommand("DELETE FROM AccountType WHERE companyId={$this->id}")->execute();
		//Trans::model()->deleteAll('companyId=:companyId', array(':companyId'=> $this->id));
		$criteria=new CDbCriteria();
		$criteria->limit=100;
		$criteria->condition='companyId=:companyId';
		$criteria->params=array(':companyId'=> $this->id);
		do{
			$transList=Trans::model()->findAll($criteria);
			if($transList!=null){
				foreach($transList as $model){
					$model->delete();
				}
			}
		}while($transList!=null);
		/*$transList=Trans::model()->findAll('companyId=:companyId', array(':companyId'=> $this->id));
		if($transList!=null){
			foreach($transList as $model){
				$model->delete();
			}
		}*/
		$this->dbConnection->createCommand("DELETE FROM Options WHERE companyId={$this->id}")->execute();
		$this->dbConnection->createCommand("DELETE FROM ChangeLog WHERE companyId={$this->id}")->execute();
		$reportList=Report::model()->findAll('companyId=:companyId', array(':companyId'=> $this->id));
		if($reportList!=null){
			foreach($reportList as $model){
				$model->delete();
			}
		}
		$this->dbConnection->createCommand("DELETE FROM CompanyUser WHERE companyId={$this->id}")->execute();
	}
}