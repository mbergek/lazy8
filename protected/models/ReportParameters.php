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


class ReportParameters extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'ReportParameters':
	 * @var integer $id
	 * @var integer $reportId
	 * @var integer $sortOrder
	 * @var string $name
	 * @var string $desc
	 * @var string $alias
	 * @var string $dataType
	 * @var string $phpSecondaryInfo
	 * @var string $isDefaultPhp
	 * @var string $defaultValue
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
		return 'ReportParameters';
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
			array('reportId, sortOrder,isDecimal,isDate', 'numerical', 'integerOnly'=>true),
			array('name, alias', 'length', 'max'=>100),
			array('desc', 'length', 'max'=>255),
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
			'name' => Yii::t('lazy8','Name'),
			'desc' => Yii::t('lazy8','Desc'),
			'alias' => Yii::t('lazy8','Alias'),
			'dataType' => Yii::t('lazy8','Data Type'),
			'phpSecondaryInfo' => Yii::t('lazy8','Secondary info in php code'),
			'isDefaultPhp' => Yii::t('lazy8','Is Default PHP'),
			'defaultValue' => Yii::t('lazy8','Default Value'),
			'isDate' => Yii::t('lazy8','Is Date'),
			'isDecimal' => Yii::t('lazy8','Is Decimal'),
			'actions' => Yii::t('lazy8','Actions'),
		);
	}
}