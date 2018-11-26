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


class ReportRows extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'ReportRows':
	 * @var integer $id
	 * @var integer $reportId
	 * @var integer $sortOrder
	 * @var string $fieldName
	 * @var string $fieldCalc
	 * @var string $fieldWidth
	 * @var integer $row
	 * @var integer $isSummed
	 * @var integer $isAlignRight
	 * @var integer $isDate
	 * @var integer $isDecimal
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
		return 'ReportRows';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('reportId', 'required'),
			array('reportId, sortOrder, row, isSummed, isAlignRight,isDecimal,isDate', 'numerical', 'integerOnly'=>true),
			array('fieldName', 'length', 'max'=>100),
			array('fieldWidth', 'length', 'max'=>10),
			array('fieldCalc', 'safe'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('lazy8','Id'),
			'reportId' => Yii::t('lazy8','Report'),
			'sortOrder' => Yii::t('lazy8','Sort Order'),
			'fieldName' => Yii::t('lazy8','Field Name'),
			'fieldCalc' => Yii::t('lazy8','Field Calc'),
			'fieldWidth' => Yii::t('lazy8','Field Width'),
			'row' => Yii::t('lazy8','Row'),
			'isSummed' => Yii::t('lazy8','Is Summed'),
			'isAlignRight' => Yii::t('lazy8','Is Align Right'),
			'isDate' => Yii::t('lazy8','Is Date'),
			'isDecimal' => Yii::t('lazy8','Is Decimal'),
		);
	}
}