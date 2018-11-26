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


class TempTrans extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Trans':
	 * @var integer $id
	 * @var integer $rownum
	 * @var string $invDate
	 * @var string $regDate
	 * @var integer $periodNum
	 * @var integer $companyNum
	 * @var string $notesheader
	 * @var string $fileInfo
	 * @var integer $accountId
	 * @var integer $customerId
	 * @var string $notes
	 * @var double $amountdebit
	 * @var double $amountcredit
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
		return 'TempTrans';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('notes,notesheader, fileInfo', 'length', 'max'=>255),
			array('invDate,regDate', 'safe'),
			array('rownum,accountId, customerId', 'numerical', 'integerOnly'=>true),
			array('amountdebit,amountcredit', 'numerical'),
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
			'companyId' => Yii::t('lazy8','Company'),
			'regDate' => Yii::t('lazy8','Reg Date'),
			'invDate' => Yii::t('lazy8','Inv Date'),
			'periodNum' => Yii::t('lazy8','Trans Num'),
			'companyNum' => Yii::t('lazy8','Trans Num'),
			'notesheader' => Yii::t('lazy8','Notes'),
			'fileInfo' => Yii::t('lazy8','File Info'),
			'accountId' => Yii::t('lazy8','Account'),
			'customerId' => Yii::t('lazy8','Customer'),
			'notes' => Yii::t('lazy8','Notes'),
			'amount' => Yii::t('lazy8','Amount'),
			'credit' => Yii::t('lazy8','Credit'),
			'debit' => Yii::t('lazy8','Debit'),
			'actions' => Yii::t('lazy8','Actions'),
			'changedBy' => Yii::t('lazy8','Changed by'),
			'dateChanged' => Yii::t('lazy8','Date Changed'),
		);
	}
}