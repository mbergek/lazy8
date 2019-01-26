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


class TransRow extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'TransRow':
	 * @var integer $id
	 * @var integer $transId
	 * @var integer $accountId
	 * @var integer $customerId
	 * @var string $notes
	 * @var double $amount
	 */

	 public function toString(){
	 	 
	 	 return 'id=' . $this->id . ';'
	 	 	.$this->accountId!=0&&isset($this->account)?'account='.$this->account->code .  ';' . $this->account->name .  ';' . $this->accountId .';':""
	 	 	.$this->customerId!=0&&isset($this->customer)?'customer='.$this->customer->code .  ';' . $this->customer->name .  ';' . $this->customerId . ';' : ""
	 	 	.'notes='.$this->notes .  ';' 
	 	 	.'amount='.$this->amount ;
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
		return 'TransRow';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('transId, accountId', 'required'),
			array('transId, accountId, customerId', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('notes', 'length', 'max'=>255),
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
			'customer'=>array(self::BELONGS_TO, 'Customer', 'customerId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('lazy8','Id'),
			'transId' => Yii::t('lazy8','Trans'),
			'accountId' => Yii::t('lazy8','Account'),
			'customerId' => Yii::t('lazy8','Customer'),
			'notes' => Yii::t('lazy8','Notes'),
			'amount' => Yii::t('lazy8','Amount'),
			'actions' => Yii::t('lazy8','Actions'),
		);
	}
}