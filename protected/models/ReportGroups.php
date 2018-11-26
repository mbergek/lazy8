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


class ReportGroups extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'ReportGroups':
	 * @var integer $id
	 * @var integer $reportId
	 * @var integer $sortOrder
	 * @var string $breakingField 
	 * @var integer $pageBreak
	 * @var integer $showGrid
	 * @var integer $showHeader
	 * @var integer $continueSumsOverGroup
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
		return 'ReportGroups';
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
			array('reportId, sortOrder, pageBreak, showGrid, showHeader, continueSumsOverGroup', 'numerical', 'integerOnly'=>true),
			array('breakingField', 'length', 'max'=>100),
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
			'fields'=>array(self::HAS_MANY, 'ReportGroupFields', 'reportGroupId','order'=>'sortOrder'),
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
			'breakingField' => Yii::t('lazy8','Break field'),
			'pageBreak' => Yii::t('lazy8','Page Break'),
			'showGrid' => Yii::t('lazy8','Show Grid'),
			'showHeader' => Yii::t('lazy8','Show Header'),
			'continueSumsOverGroup' => Yii::t('lazy8','Continue sums over group'),
		);
	}
	public function delete()
	{
		parent::delete();
		$this->dbConnection->createCommand("DELETE FROM ReportGroupFields WHERE reportGroupId={$this->id}")->execute();
	}
}