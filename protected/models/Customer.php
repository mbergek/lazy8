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


class Customer extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Customer':
	 * @var integer $id
	 * @var integer $companyId
	 * @var integer $code
	 * @var string $name
	 * @var string $desc
	 * @var integer $accountId
	 * @var string $changedBy
	 * @var string $dateChanged
	 */
	 
	 public function toString(){
	 	 return 'id=' . $this->id . ';'
	 	 	.'code='.$this->code .  ';' 
	 	 	.'name='.$this->name .  ';' 
	 	 	.'desc='.$this->desc .  ';' 
	 	 	.'account='.$this->account->code .  ';' . $this->account->name .  ';' . $this->accountId ; 
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
		return 'Customer';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('companyId, code, name, accountId', 'required'),
			array('companyId, code, accountId', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>100),
			array('desc', 'length', 'max'=>255),
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
			'account'=>array(self::BELONGS_TO, 'Account', 'accountId'), 
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('lazy8','Id'),
			'companyId' => Yii::t('lazy8','Company'),
			'code' => Yii::t('lazy8','Code'),
			'name' => Yii::t('lazy8','Name'),
			'desc' => Yii::t('lazy8','Desc'),
			'accountId' => Yii::t('lazy8','Account'),
			'userId' => Yii::t('lazy8','User'),
			'dateChanged' => Yii::t('lazy8','Date Changed'),
			'changedBy' => Yii::t('lazy8','Changed by'),
			'actions' => Yii::t('lazy8','Actions'),
		);
	}
	public function delete()
	{
		parent::delete();
		yii::app()->onDeletePost(new Lazy8Event('Customer',$this->id));
	}
}