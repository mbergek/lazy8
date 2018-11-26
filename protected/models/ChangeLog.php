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


class ChangeLog extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'ChangeLog':
	 * @var integer $id
	 * @var string $tableName
	 * @var string $logType
	 * @var string $desc
	 * @var integer $companyId
	 * @var string $changedBy
	 * @var string $dateChanged
	 */

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
		return 'ChangeLog';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('companyId', 'numerical', 'integerOnly'=>true),
			array('tableName, changedBy', 'length', 'max'=>128),
			array('logType, dateChanged', 'safe'),
			array('dateChanged','default','value'=>new CDbExpression('NOW()'),'setOnEmpty'=>false,'on'=>'update'),
			array('dateChanged','default','value'=>new CDbExpression('NOW()'),'setOnEmpty'=>false,'on'=>'insert'),
			array('changedBy','default','value'=>Yii::app()->user->name),
			array('companyId','default','value'=>Yii::app()->user->getState('selectedCompanyId')),
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
			'company'=>array(self::BELONGS_TO, 'Company', 'companyId'),
		);
	}
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public static function addLog($logType,$tablename,$desc)
	{
		$newMod=new ChangeLog();
		$newMod->tableName=$tablename;
		$newMod->desc=$desc;
		$newMod->logType=$logType;
		$newMod->save();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('lazy8','Id'),
			'desc' => Yii::t('lazy8','description'),
			'tableName' => Yii::t('lazy8','Table Name'),
			'logType' => Yii::t('lazy8','Log Type'),
			'companyId' => Yii::t('lazy8','Company'),
			'changedBy' => Yii::t('lazy8','changedBy'),
			'dateChanged' => Yii::t('lazy8','dateChanged'),
		);
	}
}